<?php
/**
 * @filename ServiceProvider.php
 * @author zhushaolong(zhushaolong@sdxxtop.com)
 * @date 2021/3/9
 * @time 上午11:55
 */

namespace Amorzhu\Weather;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(config('services.weather.key'));
        });

        $this->app->alias(Weather::class, 'weather');
    }

    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}