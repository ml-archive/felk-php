<?php

namespace Fuzz\Felk\Tests;

use Fuzz\Felk\Logging\QueryProfiler;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;

class QueryProfilerTest extends ApplicationTestCase
{
	public function testItCanAddQueryEventsAndSerializeThem()
	{
		$request = Mockery::mock(Request::class);

		DB::shouldReceive('enableQueryLog')->once();

		$request->shouldReceive('getMethod')->once()->andReturn('GET');
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');

		$connection = Mockery::mock(Connection::class);
		$connection->shouldReceive('getName')->times(3)->andReturn('fooConnection');

		$profiler = new QueryProfiler($request);

		$profiler->addQueryEvent(new QueryExecuted('select * from foo', ['bar', 'baz'], 12.56, $connection));
		$profiler->addQueryEvent(new QueryExecuted('select * from foo1', ['bar1', 'baz1'], 13.56, $connection));
		$profiler->addQueryEvent(new QueryExecuted('select * from foo2', ['bar2', 'baz2'], 56.56, $connection));

		$expect = [
			'route'                         => 'GET foo/bar',
			'route_and_query'               => 'GET foo/bar?baz=bat',
			'total_queries'                 => 3,
			'total_query_time_milliseconds' => 12.56 + 13.56 + 56.56,
			'queries'                       => [
				[
					'query' => 'select * from foo',
					'bindings' => ['bar', 'baz'],
					'time_milliseconds' => 12.56,
				],
				[
					'query' => 'select * from foo1',
					'bindings' => ['bar1', 'baz1'],
					'time_milliseconds' => 13.56,
				],
				[
					'query' => 'select * from foo2',
					'bindings' => ['bar2', 'baz2'],
					'time_milliseconds' => 56.56,
				],
			],
		];

		$collected = $profiler->toArray();

		foreach ($expect as $key => $value) {
			$this->assertSame($value, $collected[$key]);
		}
	}

	public function testItCanAddQueryEventsAndSerializeThemSafe()
	{
		$request = Mockery::mock(Request::class);

		DB::shouldReceive('enableQueryLog')->once();

		$request->shouldReceive('getMethod')->once()->andReturn('GET');
		$request->shouldReceive('getPathInfo')->once()->andReturn('foo/bar');
		$request->shouldReceive('getQueryString')->once()->andReturn('baz=bat');

		$connection = Mockery::mock(Connection::class);
		$connection->shouldReceive('getName')->times(3)->andReturn('fooConnection');

		$profiler = new QueryProfiler($request);

		$profiler->addQueryEvent(new QueryExecuted('select * from foo', ['bar', 'baz'], 12.56, $connection));
		$profiler->addQueryEvent(new QueryExecuted('select * from foo1', ['bar1', 'baz1'], 13.56, $connection));
		$profiler->addQueryEvent(new QueryExecuted('select * from foo2', ['bar2', 'baz2'], 56.56, $connection));

		$expect = [
			'route'                         => 'GET foo/bar',
			'route_and_query'               => 'GET foo/bar?baz=bat',
			'total_queries'                 => 3,
			'total_query_time_milliseconds' => 12.56 + 13.56 + 56.56,
		];

		$collected = $profiler->toSafeArray();

		$this->assertArrayNotHasKey('queries', $collected);

		foreach ($expect as $key => $value) {
			$this->assertSame($value, $collected[$key]);
		}
	}
}