<?php

namespace App\Providers;

use App\Cores\BaseProvider;
use App\Services\Singleton\Auth;

class AppServiceProvider extends BaseProvider
{
    public function register(): void
    {
        $this->container->singleton(Auth::class, Auth::class);
        $this->mergeConfigFrom("config/oauth.php", "oauth");
    }
}
