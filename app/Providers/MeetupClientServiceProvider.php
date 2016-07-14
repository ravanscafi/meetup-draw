<?php

namespace App\Providers;

use DMS\Service\Meetup\MeetupKeyAuthClient;
use Illuminate\Support\ServiceProvider;

class MeetupClientServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Boot the meetup client for the application.
     */
    public function boot()
    {
        $this->app->bind(MeetupKeyAuthClient::class, function () {
            return MeetupKeyAuthClient::factory([
                'key' => env('MEETUP_API_KEY'),
            ]);
        });
    }
}
