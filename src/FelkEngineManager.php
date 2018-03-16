<?php

namespace Fuzz\Felk\Providers;

use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Elasticsearch\ClientBuilder;
use Fuzz\Felk\Engines\ElasticSearchEngine;
use Fuzz\Felk\Engines\NullEngine;
use Illuminate\Support\Manager;

/**
 * Class FelkEngineManager
 *
 * @package Fuzz\Felk\Providers
 */
class FelkEngineManager extends Manager
{
	/**
	 * Get a driver instance.
	 *
	 * @param  string|null $name
	 *
	 * @return mixed
	 */
	public function engine($name = null)
	{
		return $this->driver($name);
	}

	/**
	 * Create an aws-elasticsearch engine instance.
	 *
	 * @return ElasticSearchEngine
	 */
	public function createAwsElasticsearchDriver()
	{
		return new ElasticSearchEngine(
			ClientBuilder::create()->setHandler(new ElasticsearchPhpHandler(config('felk.elasticsearch.region')))
				->setHosts(config('felk.elasticsearch.config.hosts'))->build(),
			config('felk.prefix')
		);
	}

	/**
	 * Create an elasticsearch engine instance.
	 *
	 * @return ElasticSearchEngine
	 */
	public function createElasticsearchDriver()
	{
		return new ElasticSearchEngine(
			ClientBuilder::create()->setHosts(config('felk.elasticsearch.config.hosts'))->build(),
			config('felk.prefix')
		);
	}

	/**
	 * Create a Null engine instance.
	 *
	 * @return NullEngine
	 */
	public function createNullEngineDriver()
	{
		return new NullEngine;
	}

	/**
	 * Get the default felk driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['felk.driver'];
	}
}
