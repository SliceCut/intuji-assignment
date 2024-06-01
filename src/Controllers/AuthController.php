<?php

namespace App\Controllers;

use App\Cores\Request;
use App\Services\AuthService;
use Exception;

class AuthController extends BaseController
{
    public function __construct(
        protected AuthService $authService,
        protected Request $request
    ) {
    }

    public function oauthCallback()
    {
        try{
            $this->authService->oauthToken($this->request);
            return redirect("home");
        } catch (Exception $ex) {
            return $this->exceptionResponse($ex);
        }
    }

    public function oauthRedirect()
    {
        return redirect($this->authService->oauthRedirect());
    }
}
