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
            $user = $this->authService->getAuthUserInfo(
                access_token: $this->session->get("access_token")
            );
            $this->auth->setUser($user);
            return $next;
        } catch (Exception $ex) {
            if ($ex->getCode() == 401) {
                return redirect("");
            }
            throw $ex;
        }
    }
}
