<?php

namespace Fuzz\Felk\Middleware;

use Closure;
use Fuzz\Felk\Contracts\Logger;
use Fuzz\Felk\Logging\APIRequestEvent;
use Fuzz\Felk\Providers\FelkEngineManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FelkMiddleware
 *
 * FelkMiddleware dumps information about the request to the log.
 *
 * @package Fuzz\Felk\Middleware
 */
class FelkMiddleware
{
	/**
	 * Logger storage
	 *
	 * @var \Fuzz\Felk\Contracts\Logger
	 */
	private $logger;

	/**
	 * FelkMiddleware constructor.
	 *
	 * @param Logger $logger
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		return $next($request);
	}

	/**
	 * Handle some logic after the response has been sent to the browser
	 *
	 * @param \Illuminate\Http\Request                   $request
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 *
	 * @return bool
	 */
	public function terminate(Request $request, Response $response): bool
	{
		$config = config('felk');

		if (! App::environment($config['enabled_environments']) || $request->header('User-Agent') === 'ELB-HealthChecker 1.0') {
			return false;
		}

		$event = APIRequestEvent::factory($request, $response, time());

		$this->logger->write($event);

		return true;
	}
}
