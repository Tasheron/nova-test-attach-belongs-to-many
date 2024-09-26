<?php

namespace Tasheron\AttachBelongsToMany;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('attach-belongs-to-many', __DIR__.'/../dist/js/field.js');
            Nova::style('attach-belongs-to-many', __DIR__.'/../dist/css/field.css');
        });
    }

    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/attach-belongs-to-many')
            ->group(__DIR__.'/../routes/api.php');
    }

    public function register()
    {
        //
    }
}
