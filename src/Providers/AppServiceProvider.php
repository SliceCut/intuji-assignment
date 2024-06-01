<?php

namespace App\Providers;

use App\Cores\BaseProvider;

class AppServiceProvider extends BaseProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom("config/oauth.php", "oauth");
    }
}
