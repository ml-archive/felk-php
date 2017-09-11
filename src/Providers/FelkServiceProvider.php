<?php

namespace Fuzz\Felk\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Fuzz\Felk\Contracts\Logger;
use Fuzz\Felk\Logging\ElasticSearchLogger;

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
		$config = config('felk');

		if (! App::environment($config['enabled_environments'])) {
			return;
		}

		$this->app->singleton(Logger::class, function() use ($config) {
			return ElasticSearchLogger::factory(
				$config['elastic_search_hosts'],
				$config['app_name']
			);
		});
	}
}