<?php

namespace Fuzz\Felk\Tests;

use Carbon\Carbon;
use Fuzz\Felk\Logging\APIRequestEvent;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

class APIRequestEventTest extends TestCase
{
	public function testItCreatesFromFactory()
	{
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$time = 604506;

		$request_headers = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getRequestUri')->once()->andReturn('foo/bar?baz=bat');

		$response_headers = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$event = APIRequestEvent::factory($request, $response, $time);

		$this->assertSame($request, $event->getRequest());
		$this->assertSame($response, $event->getResponse());
		$this->assertSame($time, $event->getTimestamp());
		$this->assertSame($time, $event->getTime()->timestamp);
	}

	public function testTimestampDefaultsToNow()
	{
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$buffer = 100;

		$request_headers = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getRequestUri')->once()->andReturn('foo/bar?baz=bat');

		$response_headers = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$event = APIRequestEvent::factory($request, $response);

		$now = time();

		$this->assertTrue((($now - $buffer) <= $event->getTimestamp()) && ($event->getTimestamp() <= ($now + $buffer)));
	}

	public function testItSerializesToArray()
	{
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$time = time();

		$request_headers = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getRequestUri')->once()->andReturn('foo/bar?baz=bat');

		$response_headers = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$event = APIRequestEvent::factory($request, $response, $time);

		$request->shouldReceive('method')->once()->andReturn('GET');
		$request->shouldReceive('getHttpHost')->once()->andReturn('https://felk.com');
		$request->shouldReceive('getContent')->once()->andReturn('foo=bar&baz=foo');
		$request->shouldReceive('ip')->once()->andReturn('52.63.25.56');
		$request->shouldReceive('getScheme')->once()->andReturn('https');
		$request->shouldReceive('getPort')->once()->andReturn('80');
		putenv('APP_ENV=some_cool_test_env');

		$response->shouldReceive('getContent')->once()->andReturn('baz=foo&foo=bar');

		$expect = [
			'timestamp'        => Carbon::createFromTimestamp($time)->toIso8601String(),
			'method'           => 'GET',
			'host'             => 'https://felk.com',
			'route'            => 'foo/bar?baz=bat',
			'status_code'      => 200,
			'request_headers'  => json_encode([
				'req_foo' => ['bar'],
				'req_baz' => ['bat'],
			]),
			'request_body'     => 'foo=bar&baz=foo',
			'response_headers' => json_encode([
				'res_foo' => ['bar'],
				'res_baz' => ['bat'],
			]),
			'response_body'    => 'baz=foo&foo=bar',
			'ip'               => '52.63.25.56',
			'scheme'           => 'https',
			'port'             => '80',
			'environment'      => 'some_cool_test_env',
		];

		$this->assertSame($expect, $event->toArray());

		// Unset APP_ENV
		putenv('APP_ENV');
	}

	public function testItSerializesToJSON()
	{
		$request  = Mockery::mock(Request::class);
		$response = Mockery::mock(Response::class);
		$time = time();

		$request_headers = Mockery::mock(HeaderBag::class);
		$request->headers = $request_headers;
		$request_headers->shouldReceive('all')->once()->andReturn([
			'req_foo' => ['bar'],
			'req_baz' => ['bat'],
		]);
		$request->shouldReceive('getRequestUri')->once()->andReturn('foo/bar?baz=bat');

		$response_headers = Mockery::mock(HeaderBag::class);
		$response->headers = $response_headers;
		$response_headers->shouldReceive('all')->once()->andReturn([
			'res_foo' => ['bar'],
			'res_baz' => ['bat'],
		]);
		$response->shouldReceive('getStatusCode')->once()->andReturn(200);

		$event = APIRequestEvent::factory($request, $response, $time);

		$request->shouldReceive('method')->once()->andReturn('GET');
		$request->shouldReceive('getHttpHost')->once()->andReturn('https://felk.com');
		$request->shouldReceive('getContent')->once()->andReturn('foo=bar&baz=foo');
		$request->shouldReceive('ip')->once()->andReturn('52.63.25.56');
		$request->shouldReceive('getScheme')->once()->andReturn('https');
		$request->shouldReceive('getPort')->once()->andReturn('80');
		putenv('APP_ENV=some_cool_test_env');

		$response->shouldReceive('getContent')->once()->andReturn('baz=foo&foo=bar');

		$expect = json_encode([
			'timestamp'        => Carbon::createFromTimestamp($time)->toIso8601String(),
			'method'           => 'GET',
			'host'             => 'https://felk.com',
			'route'            => 'foo/bar?baz=bat',
			'status_code'      => 200,
			'request_headers'  => json_encode([
				'req_foo' => ['bar'],
				'req_baz' => ['bat'],
			]),
			'request_body'     => 'foo=bar&baz=foo',
			'response_headers' => json_encode([
				'res_foo' => ['bar'],
				'res_baz' => ['bat'],
			]),
			'response_body'    => 'baz=foo&foo=bar',
			'ip'               => '52.63.25.56',
			'scheme'           => 'https',
			'port'             => '80',
			'environment'      => 'some_cool_test_env',
		]);

		$this->assertSame($expect, $event->toJson());

		// Unset APP_ENV
		putenv('APP_ENV');
	}
}