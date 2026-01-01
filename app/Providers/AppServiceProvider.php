<?php

namespace App\Providers;

use App\Models\Complaint;
use App\Observers\ComplaintObserver;
use App\Channels\WhatsAppChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

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
        // Register model observers for extended audit logging
        Complaint::observe(ComplaintObserver::class);

        // Register WhatsApp notification channel
        Notification::extend('whatsapp', function ($app) {
            return $app->make(WhatsAppChannel::class);
        });
    }
}
