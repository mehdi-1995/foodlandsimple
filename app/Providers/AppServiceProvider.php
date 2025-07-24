<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); // برای همه فیلدهای رشته‌ای
        $this->app->singleton(\Faker\Generator::class, function () {
            $faker = FakerFactory::create('fa_IR');
            $faker->addProvider(new \App\Providers\FarsiFakerProvider($faker));
            return $faker;
        });
    }
}
