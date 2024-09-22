<?php

namespace App\Providers;

use app\Repositories\BookingRepository;
use app\Repositories\CityRepository;
use App\Repositories\Contracts\BookingRepositoryInterface;
use app\Repositories\Contracts\CityRepositoryInterface;
use app\Repositories\Contracts\GymRepositoryInterface;
use app\Repositories\Contracts\SubscribePackageRepositoryInterface;
use app\Repositories\GymRepository;
use app\Repositories\SubscribePackageRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->singleton(CityRepositoryInterface::class, CityRepository::class);
        $this->app->singleton(GymRepositoryInterface::class, GymRepository::class);
        $this->app->singleton(SubscribePackageRepositoryInterface::class, SubscribePackageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
