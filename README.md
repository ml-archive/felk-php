Fuzz Felk for PHP/Laravel
=========================

## Installation
```bash
$ composer require fuzz/felk-php
```

## Configuration
1. Add the service provider to `config/app.php`
    ```
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
    ```
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