<?php

namespace Fuzz\Felk\Tests;

use Fuzz\Felk\Engines\ElasticSearchEngine;
use Fuzz\Felk\Logging\APIRequestEvent;
use Fuzz\Felk\Logging\ElasticSearchLogger;
use Fuzz\Felk\Middleware\FelkMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mockery;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

class FelkMiddlewareTest extends ApplicationTestCase
{
	public function testItChecksAppEnvironmentBeforeAttemptingToRun()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		$request           = Mockery::mock(Request::class);
		$response          = Mockery::mock(Response::class);
		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;

		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(false);

		$logger = Mockery::mock(ElasticSearchEngine::class);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(false);

		$logger->shouldReceive('write')->never();

		$this->assertFalse($middleware->terminate($request, $response));
	}

	public function testItWritesEventToLogger()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$request_headers  = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');
		$request->shouldReceive('header')->with('User-Agent')->once()->andReturn('Some User Agent');

		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(false);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(true);

		$logger->shouldReceive('write')->with(Mockery::on(function ($arg) {
			return $arg instanceof APIRequestEvent;
		}), true)->once();

		$this->assertTrue($middleware->terminate($request, $response));

		putenv('APP_ENV');
	}

	public function testItWritesEventToLoggerWithForceSafeByDefault()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		// Ignore force_safe
		$this->app['config']->set('felk', [
			'elastic_search_hosts' => ['https://felk.com:443'],
			'enabled_environments' => [
				'local',
				'dev',
				'staging',
			],
			'app_name'             => 'FooApp',
		]);

		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$request_headers  = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');
		$request->shouldReceive('header')->with('User-Agent')->once()->andReturn('Some User Agent');

		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(false);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(true);

		$logger->shouldReceive('write')->with(Mockery::on(function ($arg) {
			return $arg instanceof APIRequestEvent;
		}), true)->once();

		$this->assertTrue($middleware->terminate($request, $response));

		putenv('APP_ENV');
	}

	public function testItWritesEventToLoggerWithForceSafeOff()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		// Disable force_safe
		$this->app['config']->set('felk', [
			'elastic_search_hosts' => ['https://felk.com:443'],
			'force_safe'           => false,
			'enabled_environments' => [
				'local',
				'dev',
				'staging',
			],
			'app_name'             => 'FooApp',
		]);

		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$request_headers  = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');
		$request->shouldReceive('header')->with('User-Agent')->once()->andReturn('Some User Agent');

		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(false);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(true);

		$logger->shouldReceive('write')->with(Mockery::on(function ($arg) {
			return $arg instanceof APIRequestEvent;
		}), false)->once();

		$this->assertTrue($middleware->terminate($request, $response));

		putenv('APP_ENV');
	}

	public function testItLogsRequestIdIfItIsAvailable()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$request_headers  = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');
		$request->shouldReceive('header')->with('User-Agent')->once()->andReturn('Some User Agent');

		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(true);
		$response_headers->shouldReceive('get')->with('X-Request-Id')->once()->andReturn('fooRequestId');
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(true);

		$logger->shouldReceive('write')->with(Mockery::on(function ($arg) {
			return $arg instanceof APIRequestEvent && $arg->getRequestId() === 'fooRequestId';
		}), true)->once();

		$this->assertTrue($middleware->terminate($request, $response));

		putenv('APP_ENV');
	}

	public function testItChecksUserAgentForHealthCheckerBeforeAttemptingToRun()
	{
		if (! defined('LARAVEL_START')) {
			define('LARAVEL_START', microtime(true));
		}

		$request           = Mockery::mock(Request::class);
		$response          = Mockery::mock(Response::class);
		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;

		$response_headers->shouldReceive('has')->with('X-Request-Id')->once()->andReturn(false);
		$logger = Mockery::mock(ElasticSearchEngine::class);

		$request->shouldReceive('header')->with('User-Agent')->once()
				->andReturn(FelkMiddleware::ELB_HEALTH_CHECKER_AGENT);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with([
			'local',
			'dev',
			'staging',
		])->once()->andReturn(true);

		$this->assertFalse($middleware->terminate($request, $response));

		putenv('APP_ENV');
	}


	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application $app
	 *
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('felk', [
			'elastic_search_hosts' => ['https://felk.com:443'],
			'enabled_environments' => [
				'local',
				'dev',
				'staging',
			],
			'app_name'             => 'FooApp',
		]);
	}
}