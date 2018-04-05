<?php

namespace Fuzz\Felk\Engines;

use Elasticsearch\Client;
use Fuzz\Felk\Contracts\LoggableEvent;
use Fuzz\Felk\Contracts\Logger;

/**
 * Class ElasticSearchEngine
 *
 * @package Fuzz\Felk\Engines
 */
class ElasticSearchEngine implements Logger
{
	/**
	 * ElasticSearch client storage
	 *
	 * @var \Elasticsearch\Client
	 */
	protected $es;

	/**
	 * The prefix for the logging index.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * ElasticSearchEngine constructor.
	 *
	 * @param Client $client
	 * @param string $prefix
	 */
	public function __construct(Client $client, string $prefix)
	{
		$this->es     = $client;
		$this->prefix = strtolower($prefix);
	}

	/**
	 * Log an event to the store
	 *
	 * @param \Fuzz\Felk\Contracts\LoggableEvent $event
	 * @param bool                               $force_safe
	 *
	 * @return array
	 */
	public function write(LoggableEvent $event, bool $force_safe = true): array
	{
		$response = $this->es->index([
			'index' => "{$this->prefix}_{$event->getType()}",
			'type'  => $event->getType(),
			'id'    => $event->getUniqueId(),
			'body'  => $force_safe ? $event->toSafeArray() : $event->toArray(),
		]);

		return $response;
	}
}
