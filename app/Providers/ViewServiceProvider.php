<?php

namespace App\Providers;

use App\Enums\PriorityEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::share([
            'status'   => StatusEnum::class,
            'priority' => PriorityEnum::class,
        ]);
    }
}
