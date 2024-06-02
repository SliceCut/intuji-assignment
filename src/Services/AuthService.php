<?php

namespace App\Services;

use App\Cores\Request;
use App\Cores\Session;
use App\Services\Singleton\Auth;
use App\Traits\HandleException;
use Exception;
use InvalidArgumentException;

class AuthService
{
    use HandleException;

    public function __construct(
        protected HttpService $httpService,
        protected Session $session,
        protected Auth $auth
    ) {
    }

    public function oauthToken(Request $request): array
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
                "grant_type" => "authorization_code"
            ]
        );

        $this->throwException($response);

        $access_token = $response["payload"]["access_token"];
        $refresh_token = $response["payload"]["refresh_token"];

        $this->session->put("access_token", $access_token);
        $this->session->put("refresh_token", $refresh_token);

        return [
            "access_token" => $access_token,
            "refresh_token" => $refresh_token
        ];
    }

    public function oauthRefreshToken(string $refresh_token): array
    {
        $response = $this->httpService->post(
            url: config("oauth.token_url"),
            request_data: [
                "refresh_token" => $refresh_token,
                "client_id" => config("oauth.client_id"),
                "client_secret" => config("oauth.client_secret"),
                "redirect_uri" => config("oauth.redirect_uri"),
                "grant_type" => "refresh_token"
            ]
        );

        $this->throwException($response);

        $access_token = $response["payload"]["access_token"];

        $this->session->put("access_token", $access_token);
        $this->session->put("refresh_token", $refresh_token);

        return [
            "access_token" => $access_token,
            "refresh_token" => $refresh_token
        ];
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

    public function oauthRevokeToken(string $token): void
    {
        $response = $this->httpService->post(
            url: config("oauth.revoke_token_url"),
            request_data: [
                "token" => $token
            ]
        );
        $this->throwException($response);
    }

    public function logout(): void
    {
        $this->oauthRevokeToken(
            token: $this->auth->token()
        );
        $this->clearSession();
    }

    public function clearSession()
    {
        $this->session->delByKey("access_token");
        $this->session->delByKey("refresh_token");
    }
}
