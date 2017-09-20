<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Default Logging Engine
	|--------------------------------------------------------------------------
	|
	| This option controls the default logging connection that gets used while
	| using Felk. This connection is used when logging requests and responses.
	| You should adjust this based on your needs.
	|
	| Supported: "aws-elasticsearch", "elasticsearch", "null"
	|
	*/

	'driver'        => env('FELK_DRIVER', 'elasticsearch'),

	/*
	|--------------------------------------------------------------------------
	| Elasticsearch Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure your elasticsearch settings.
	|
	*/
	'elasticsearch' => [
		'region' => env('AWS_ELASTICSEARCH_REGION', 'us-east-1'), // Only needed when using aws-elasticsearch provider.
		'config' => [
			'hosts' => [
				[
					'host'   => env('ELASTICSEARCH_HOST', 'localhost'),
					'port'   => env('ELASTICSEARCH_PORT', 9200),
					'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
					'user'   => env('ELASTICSEARCH_USERNAME', ''),
					'pass'   => env('ELASTICSEARCH_PASSWORD', ''),
				],
			],
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Enabled Environments
	|--------------------------------------------------------------------------
	|
	| All environments where bib should be enabled
	*/

	'enabled_environments' => ['local', 'dev', 'staging'],

	/*
	|--------------------------------------------------------------------------
	| Prefix
	|--------------------------------------------------------------------------
	|
	| Here you may specify a prefix that will be applied to all logging records
	| recorded by Felk. This prefix may be useful if you have multiple
	| "tenants" or applications sharing the same logging infrastructure.
	|
	*/

	'prefix' => env('APP_NAME'),
];
