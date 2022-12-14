<?php

namespace hanymigame\SmsRuChannel;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

/**
 *
 */
class SmsRuServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(SmsRuApi::class, static function ($app) {
            return new SmsRuApi($app['config']['sms_ru_channel'], new HttpClient([
                'base_uri' => 'https://sms.ru/',
                'timeout' => 5,
                'connect_timeout' => 5,
            ]));
        });
    }
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/sms_ru_channel.php' => $this->app->configPath('sms_ru_channel.php'),
            ], 'sms_ru_channel');
        }
    }
}