<?php

namespace App\Middlewares;

use App\Cores\Middleware;
use App\Cores\Request;
use App\Cores\Session;
use App\Services\AuthService;
use App\Services\Singleton\Auth;
use Exception;

class Authenticate extends Middleware
{
    public function __construct(
        private AuthService $authService,
        protected Session $session,
        protected Auth $auth
    ) {
    }

    public function handle($next, Request $request)
    {
        try {
            $access_token = $this->session->get("access_token");
            if (!$access_token) {
                return $this->clearSessionAndRedirect();
            }
            $user = $this->authService->getAuthUserInfo(
                access_token: $access_token
            );
            $this->auth->setUser($user);
            $this->auth->setToken($access_token);
            return $next;
        } catch (Exception $ex) {
            if ($ex->getCode() == 401) {
                return $this->handleRefreshToken($next, $request);
            }
            throw $ex;
        }
    }

    protected function handleRefreshToken($next, $request): mixed
    {
        $refresh_token = $this->session->get("refresh_token");
        if ($refresh_token) {
            try {
                $response = $this->authService->oauthRefreshToken($refresh_token);
                $this->auth->setToken($response["access_token"]);
                return $this->handle($next, $request);
            } catch (Exception $ex) {
                return $this->clearSessionAndRedirect();
            }
        }

        return $this->clearSessionAndRedirect();;
    }

    protected function clearSessionAndRedirect(): mixed
    {
        $this->authService->clearSession();
        return redirect("");
    }
}
