<?php

namespace Fuzz\Felk\Providers;

use Fuzz\Felk\Contracts\Logger;
use Illuminate\Support\ServiceProvider;

class FelkServiceProvider extends ServiceProvider
{
	/**
	 * Register any other events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		$config_file = realpath(__DIR__ . '/../../config/felk.php');
		$this->publishes([
			$config_file => config_path('felk.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(FelkEngineManager::class, function($app) {
			return new FelkEngineManager($app);
		});

		$this->app->singleton(Logger::class, function($app) {
			// First, we will create the Felk manager which is responsible for the
			// creation of the various felk drivers when they are needed by the
			// application instance, and will resolve them on a lazy load basis.
			/** @var FelkEngineManager $manager */
			$manager = $app[FelkEngineManager::class];

			return $manager->driver();
		});
	}
}
