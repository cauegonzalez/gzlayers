<?php

namespace CaueGonzalez\GzLayers;

use Illuminate\Support\ServiceProvider;

class GzLayersServiceProvider extends ServiceProvider
{
    protected $commands = [
        'CaueGonzalez\GzLayers\Commands\ModelGenerator',
        'CaueGonzalez\GzLayers\Commands\ControllerGenerator',
        'CaueGonzalez\GzLayers\Commands\ResourceGenerator',
        'CaueGonzalez\GzLayers\Commands\BOGenerator',
        'CaueGonzalez\GzLayers\Commands\RepositoryGenerator',
        'CaueGonzalez\GzLayers\Commands\RequestGenerator',
        'CaueGonzalez\GzLayers\Commands\TraitGenerator',
        'CaueGonzalez\GzLayers\Commands\CRUDGenerator',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }
}
