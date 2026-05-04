<?php

namespace Jeoip\Ip2Location;

use GeoIp2\Database\Reader;
use Illuminate\Support\ServiceProvider;
use Jeoip\Contracts\IGeoIPService;

class GeoIPServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/geoip.php', 'geoip');

        $this->app->singleton('geoip.reader.city', function ($app) {
            return $this->makeReader($app['config']->get('geoip.databases.city'), 'city');
        });

        $this->app->singleton('geoip.reader.asn', function ($app) {
            return $this->makeReader($app['config']->get('geoip.databases.asn'), 'asn');
        });

        $this->app->singleton('geoip.reader.country', function ($app) {
            $path = $app['config']->get('geoip.databases.country');
            if (!is_string($path) || !is_file($path)) {
                return null;
            }

            return new Reader($path, $app['config']->get('geoip.locales', ['en']));
        });

        $this->app->bind(IGeoIPService::class, function ($app) {
            return new GeoIPService(
                $app->make('geoip.reader.city'),
                $app->make('geoip.reader.asn'),
                $app->make('geoip.reader.country'),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/geoip.php' => function_exists('config_path')
                ? config_path('geoip.php')
                : base_path('config/geoip.php'),
        ], 'geoip-config');
    }

    private function makeReader(mixed $path, string $kind): Reader
    {
        if (!is_string($path) || '' === $path) {
            throw new \RuntimeException("GeoIP {$kind} database path is not configured (geoip.databases.{$kind}).");
        }
        if (!is_file($path)) {
            throw new \RuntimeException("GeoIP {$kind} database file not found at: {$path}");
        }

        return new Reader($path, config('geoip.locales', ['en']));
    }
}
