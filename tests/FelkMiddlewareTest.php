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
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with(['local', 'dev', 'staging'])->once()->andReturn(false);

		$logger->shouldReceive('write')->never();

		$this->assertFalse($middleware->terminate($request, $response));
	}

	public function testItWritesEventToLogger()
	{
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$logger   = Mockery::mock(ElasticSearchEngine::class);

		$request_headers  = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getRequestUri')->once()->andReturn('foo/bar?baz=bat');

		$response_headers  = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$middleware = new FelkMiddleware($logger);

		App::shouldReceive('environment')->with(['local', 'dev', 'staging'])->once()->andReturn(true);

		$logger->shouldReceive('write')->with(Mockery::on(function ($arg) {
			return $arg instanceof APIRequestEvent;
		}))->once();

		$this->assertTrue($middleware->terminate($request, $response));

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