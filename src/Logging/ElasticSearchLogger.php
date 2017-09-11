<?php

namespace Fuzz\Felk\Logging;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Fuzz\Felk\Contracts\LoggableEvent;
use Fuzz\Felk\Contracts\Logger;

/**
 * Class ElasticSearchLogger
 *
 * @package Fuzz\Felk\Logging
 */
class ElasticSearchLogger implements Logger
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
	private $es;

	/**
	 * Application name
	 *
	 * @var string
	 */
	private $app_name;

	/**
	 * Index
	 *
	 * @var string
	 */
	private $index;

	/**
	 * ElasticSearchLogger constructor.
	 *
	 * @param \Elasticsearch\Client $client
	 * @param string                $app_name
	 */
	public function __construct(Client $client, string $app_name)
	{
		$this->es       = $client;
		$this->app_name = $app_name;
		$this->index    = strtolower("{$this->app_name}_felk");
	}

	/**
	 * ElasticSearchLogger factory.
	 * f
	 *
	 * @param array  $hosts
	 * @param string $app_name
	 *
	 * @return \Fuzz\Felk\Logging\ElasticSearchLogger
	 */
	public static function factory(array $hosts, string $app_name)
	{
		$client = ClientBuilder::create()->setHosts($hosts)->build();

		return new self($client, $app_name);
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
			'index' => $this->index,
			'type'  => self::TYPE,
			'id'    => $event->getUniqueId(),
			'body'  => $event->toArray(),
		]);

		return $response;
	}
}
