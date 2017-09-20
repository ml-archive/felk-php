<?php

namespace Fuzz\Felk\Engines;

use Fuzz\Felk\Contracts\LoggableEvent;
use Fuzz\Felk\Contracts\Logger;

/**
 * Class ElasticSearchEngine
 *
 * @package Fuzz\Felk\Engines
 */
class NullEngine implements Logger
{
	/**
	 * Document type
	 *
	 * @const string
	 */
	const TYPE = 'felk_log';

	/**
	 * The prefix for the logging index.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Index
	 *
	 * @var string
	 */
	protected $index;

	/**
	 * Get the index to log to.
	 *
	 * @return string
	 */
	public function index()
	{
		return '';
	}

	/**
	 * Log an event to the store
	 *
	 * @param \Fuzz\Felk\Contracts\LoggableEvent $event
	 *
	 * @return array
	 */
	public function write(LoggableEvent $event): array
	{
		return [];
	}
}
