Felk for PHP/Laravel[![Build Status](https://img.shields.io/travis/fuzz-productions/felk-php/master.svg?style=flat)](https://travis-ci.org/fuzz-productions/felk-php) [![Slack Status](https://fuzz-opensource.herokuapp.com/badge.svg)](https://fuzz-opensource.herokuapp.com/)
=========================
FELK is a helper library which can take data from a Laravel request/response and push it to a store (currently ElasticSearch, hence Fuzz [ELK](https://www.elastic.co/webinars/introduction-elk-stack)). It is intended to only be used in development environments to debug and run analytics on API requests/responses. 


## Installation
```bash
$ composer require fuzz/felk-php
```

## Configuration
1. Add the service provider to `config/app.php`
    ```php
    'providers' => [
        ...
        
        /*
         * Application Service Providers...
         */
        \Fuzz\Felk\Providers\FelkServiceProvider::class,
        
        ...
    ],
    ```
1. Publish the vendor config `$ php artisan vendor:publish --provider="Fuzz\Felk\Providers\FelkServiceProvider"`
1. Configure the configuration variables in `config/felk.php`
1. Add the Felk middleware to the middleware stack in `app/Http/Kernel.php`
    ```php
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        ...
        \Fuzz\Felk\Middleware\FelkMiddleware::class,
        ...
    ];
    ```
1. Check ElasticSearch.
