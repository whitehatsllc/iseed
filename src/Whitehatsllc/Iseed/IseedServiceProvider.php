<?php

namespace Whitehatsllc\Iseed;

use Illuminate\Support\ServiceProvider;

class IseedServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        require base_path() . '/vendor/autoload.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerResources();

        $this->app['iseed'] = $this->app->share(function($app) {
            return new Iseed;
        });
//        $this->app->booting(function() {
//            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//            $loader->alias('Iseed', 'Whitehatsllc\Iseed\Facades\Iseed');
//        });

        $this->app['command.iseed'] = $this->app->share(function($app) {
            return new IseedCommand;
        });
        $this->commands('command.iseed');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('iseed');
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $userConfigFile = $this->configPath() . '/iseed.php';
        $packageConfigFile = __DIR__ . '/../../config/config.php';
        $config = $this->app['files']->getRequire($packageConfigFile);

        if (file_exists($userConfigFile)) {
            $userConfig = $this->app['files']->getRequire($userConfigFile);
            $config = array_replace_recursive($config, $userConfig);
        }

        $this->app['config']->set('iseed::config', $config);
    }

    private function configPath($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }

}
