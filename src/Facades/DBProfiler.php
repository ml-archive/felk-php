<?php

namespace Fuzz\Felk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ActionLogger
 *
 * @method static addQueryEvent(\Illuminate\Database\Events\QueryExecuted $query)
 * @method static \Fuzz\Felk\Contracts\LoggableEvent getLoggableEvent()
 *
 * @package Fuzz\ApiServer\Logging\Facades
 */
class DBProfiler extends Facade
{
	/**
	 * Get the facade accessor
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return self::class;
	}
}
