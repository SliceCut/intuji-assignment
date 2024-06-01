<?php

namespace App\Services;

use App\Cores\Request;
use App\Cores\Session;
use App\Exceptions\UnauthorizedException;
use Exception;
use InvalidArgumentException;

class AuthService
{
    public function __construct(
        protected HttpService $httpService,
        protected Session $session
    ) {
    }

    public function oauthToken(Request $request): void
    {
        if ($error = $request->get("error")) {
            throw new Exception($error, 400);
        }

        if (!$request->get("code")) {
            throw new InvalidArgumentException("Code is missing from the request.", 400);
        }

        $response = $this->httpService->post(
            url: config("oauth.token_url"),
            request_data: [
                "code" => $request->get("code"),
                "client_id" => config("oauth.client_id"),
                "client_secret" => config("oauth.client_secret"),
                "redirect_uri" => config("oauth.redirect_uri"),
                "grant_type" => config("oauth.grant_type")
            ]
        );

        $this->throwException($response);

        $access_token = $response["payload"]["access_token"];
        $refresh_token = $response["payload"]["refresh_token"];

        $this->session->put("access_token", $access_token);
        $this->session->put("refresh_token", $refresh_token);
        $this->session->put("user_info", $this->getAuthUserInfo($access_token));
    }

    public function oauthRedirect(): string
    {
        $query_params = [
            "scope" => config("oauth.scope"),
            "access_type" => config("oauth.access_type"),
            "include_granted_scopes" => "true",
            "response_type" => config("oauth.response_type"),
            "state" => config("oauth.state"),
            "redirect_uri" => config("oauth.redirect_uri"),
            "client_id" => config("oauth.client_id")
        ];
        $redirect = config("oauth.authorization_url") . "?" . http_build_query($query_params);
        return $redirect;
    }

    public function getAuthUserInfo($access_token): array
    {
        $response = $this->httpService->get(
            url: config("oauth.userinfo_endpoint"),
            headers: [
                'Authorization: Bearer ' . $access_token,
            ]
        );

        $this->throwException($response);

        return $response["payload"];
    }

    /**
     * Throw a exception if response status is not 200
     * 
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function throwException($response): void
    {
        if ($response["status"] != 200) {
            if (in_array($response["status"], [401, 403])) {
                throw new UnauthorizedException(
                    message: $response["payload"]["error"],
                    code: $response["status"]
                );
            }
            throw new Exception($response["payload"]["error"], $response["status"]);
        }
    }
}
