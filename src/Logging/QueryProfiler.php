<?php

namespace Fuzz\Felk\Logging;

use Carbon\Carbon;
use Fuzz\Felk\Contracts\LoggableEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueryProfiler implements LoggableEvent
{
	/**
	 * The current route
	 *
	 * @var string
	 */
	protected $route;

	/**
	 * The current route query
	 *
	 * @var string|null
	 */
	protected $query;

	/**
	 * Total queries executed
	 *
	 * @var int
	 */
	protected $total_queries = 0;

	/**
	 * Total time spent waiting for queries to execute
	 *
	 * @var int
	 */
	protected $total_query_time_ms = 0;

	/**
	 * @var \Illuminate\Database\Events\QueryExecuted[]
	 */
	protected $queries = [];

	/**
	 * DBProfiler constructor.
	 *
	 * @param \Illuminate\Http\Request $request
	 */
	public function __construct(Request $request)
	{
		DB::enableQueryLog();

		$method      = strtoupper($request->getMethod());
		$route       = $request->getPathInfo();
		$this->query = $request->getQueryString();
		$this->route = "$method $route";
	}

	/**
	 * Add a query event to the log
	 *
	 * @param \Illuminate\Database\Events\QueryExecuted $query
	 */
	public function addQueryEvent(QueryExecuted $query)
	{
		$this->queries[]           = $query;
		$this->total_query_time_ms += $query->time;
		$this->total_queries++;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'route'                         => $this->route,
			'route_and_query'            => is_null($this->query) ? $this->route :
				$this->route . '?' . $this->query,
			'timestamp'                     => Carbon::now()->toIso8601String(),
			'total_queries'                 => $this->total_queries,
			'total_query_time_milliseconds' => $this->total_query_time_ms,
			'queries'                       => $this->getQueries(),
		];
	}

	/**
	 * Serialize queries to array
	 *
	 * @return array
	 */
	public function getQueries(): array
	{
		return array_map(function (QueryExecuted $query) {
			return [
				'query'             => $query->sql,
				'bindings'          => $query->bindings,
				'time_milliseconds' => $query->time,
			];
		}, $this->queries);
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 *
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Generate a unique ID for this event
	 *
	 * @return string
	 */
	public function getUniqueId(): string
	{
		return hash('sha256', $this->route . uniqid('', true) . round(microtime(true) * 1000));
	}

	/**
	 * Get the instance as a safe array with sensitive data removed
	 *
	 * @return array
	 */
	public function toSafeArray(): array
	{
		return [
			'route'                         => $this->route,
			'route_and_query'            => is_null($this->query) ? $this->route :
				$this->route . '?' . $this->query,
			'timestamp'                     => Carbon::now()->toIso8601String(),
			'total_queries'                 => $this->total_queries,
			'total_query_time_milliseconds' => $this->total_query_time_ms,
		];
	}

	/**
	 * The event type
	 *
	 * @return string
	 */
	public function getType(): string
	{
		return 'db_profile';
	}

	/**
	 * Get a loggable event from the query log
	 *
	 * @return \Fuzz\Felk\Contracts\LoggableEvent
	 */
	public function getLoggableEvent(): LoggableEvent
	{
		return $this;
	}
}