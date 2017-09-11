<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Elastic Search hosts
	|--------------------------------------------------------------------------
	|
	| Full hostname with protocol and port. You can supply multiple hosts.
	| Ex: https://search-felk.es.aws.com:443
	*/
	'elastic_search_hosts' => [env('FELK_HOST')],

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
	| Application Details
	|--------------------------------------------------------------------------
	|
	| All descriptors for the application
	*/
	'app_name'             => env('APP_NAME'),
];