<?php

return [
    "grant_type" => "authorization_code",
    "access_type" => env("OAUTH_ACCESS_TYPE", "online"),
    "state" => env("OAUTH_STATE", "random_state"),
    "redirect_uri" => env("OAUTH_REDIRECT_URI"),
    "response_type" => env("OAUTH_RESPONSE_TYPE", "code"),
    "scope" => env("OAUTH_SCOPE"),
    "authorization_url" => env('OAUTH_AUTHORIZATION_URL'),
    "client_id" => env("OAUTH_CLIENT_ID"),
    "client_secret" => env("OAUTH_CLIENT_SECRET"),
    "token_url" => env("OAUTH_TOKEN_URL"),
    "revoke_token_url" => env("OAUTH_REVOKE_TOKEN_URL"),
    "userinfo_endpoint" => env("OAUTH_USERINFO_ENDPOINT")
];