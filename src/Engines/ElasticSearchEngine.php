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
	 * Document type
	 *
	 * @const string
	 */
	const TYPE = 'felk_log';

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
	 * Index
	 *
	 * @var string
	 */
	protected $index;

	/**
	 * ElasticSearchEngine constructor.
	 *
	 * @param Client $client
	 * @param string $prefix
	 */
	public function __construct(Client $client, string $prefix)
	{
		$this->es     = $client;
		$this->prefix = $prefix;
		$this->index  = strtolower("{$this->prefix}_felk");
	}

	/**
	 * Get the index to log to.
	 *
	 * @return string
	 */
	public function index()
	{
		return $this->index;
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
		$response = $this->es->index([
			'index' => $this->index(),
			'type'  => self::TYPE,
			'id'    => $event->getUniqueId(),
			'body'  => $event->toArray(),
		]);

		return $response;
	}
}
