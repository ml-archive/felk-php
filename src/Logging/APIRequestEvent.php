<?php

namespace Fuzz\Felk\Logging;

use Carbon\Carbon;
use Fuzz\Felk\Contracts\LoggableEvent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class APIRequest
 *
 * @package Fuzz\Felk\Logging
 */
class APIRequestEvent implements LoggableEvent
{
	/**
	 * Headers to ignore in safe mode
	 *
	 * @const array
	 */
	const UNSAFE_HEADERS = ['authorization', 'Authorization'];

	/**
	 * Request storage
	 *
	 * @var \Illuminate\Http\Request
	 */
	private $request;

	/**
	 * Response storage
	 *
	 * @var \Symfony\Component\HttpFoundation\Response
	 */
	private $response;

	/**
	 * Status Code storage
	 *
	 * @var int
	 */
	private $status_code;

	/**
	 * Response time in milliseconds
	 *
	 * @var int
	 */
	private $response_time_ms;

	/**
	 * Headers storage
	 *
	 * @var array
	 */
	private $headers = [
		'request' => [],
		'response' => [],
	];

	/**
	 * Route storage
	 *
	 * @var string
	 */
	private $route;

	/**
	 * Carbon storage
	 *
	 * @var \Carbon\Carbon
	 */
	private $timestamp;

	/**
	 * Request ID storage
	 *
	 * @var string|null
	 */
	private $request_id = null;

	/**
	 * Build a new APIRequestEvent
	 *
	 * @param \Illuminate\Http\Request                   $request
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 * @param int                                        $response_time_ms
	 * @param int|null                                   $time
	 * @param string                                     $request_id
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public static function factory(Request $request, Response $response, int $response_time_ms = 0, int $time = null, string $request_id = null): APIRequestEvent
	{
		$event = new self;

		$event = $event->setRequest($request)
			->setResponse($response)
			->setTime(is_null($time) ? time() : $time)
			->setResponseTime($response_time_ms);

		if (! is_null($request_id)) {
			$event->setRequestId($request_id);
		}

		return $event;
	}

	/**
	 * Get the Request
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * Set the Request
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setRequest(Request $request): APIRequestEvent
	{
		$this->request = $request;
		$this->headers['request'] = $request->headers->all();

		$this->setRoute($request->getRequestUri());

		return $this;
	}

	/**
	 * Get the Response
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getResponse(): Response
	{
		return $this->response;
	}

	/**
	 * Set the Response
	 *
	 * @param \Symfony\Component\HttpFoundation\Response $response
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setResponse(Response $response): APIRequestEvent
	{
		$this->response = $response;
		$this->headers['response'] = $response->headers->all();

		$this->setStatusCode($response->getStatusCode());

		return $this;
	}

	/**
	 * Get the Route
	 *
	 * @return string
	 */
	public function getRoute(): string
	{
		return strtoupper($this->getRequest()->getMethod()) . ' ' .$this->route;
	}

	/**
	 * Set the Route
	 *
	 * @param string $route
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setRoute(string $route): APIRequestEvent
	{
		$this->route = $route;

		return $this;
	}

	/**
	 * Get the RequestID
	 *
	 * @return string
	 */
	public function getRequestId(): string
	{
		return $this->request_id;
	}

	/**
	 * Set the RequestID
	 *
	 * @param string $request_id
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setRequestId(string $request_id): APIRequestEvent
	{
		$this->request_id = $request_id;

		return $this;
	}

	/**
	 * Get the RequestHeaders
	 *
	 * @return array
	 */
	public function getRequestHeaders(): array
	{
		return $this->headers['request'];
	}

	/**
	 * Get the ResponseHeaders
	 *
	 * @return array
	 */
	public function getResponseHeaders(): array
	{
		return $this->headers['response'];
	}

	/**
	 * Get the StatusCode
	 *
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->status_code;
	}

	/**
	 * Set the StatusCode
	 *
	 * @param int $status_code
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setStatusCode(int $status_code): APIRequestEvent
	{
		$this->status_code = $status_code;

		return $this;
	}

	/**
	 * Get the Timestamp
	 *
	 * @return int
	 */
	public function getTimestamp(): int
	{
		return $this->timestamp->timestamp;
	}

	/**
	 * Get the Time
	 *
	 * @return \Carbon\Carbon
	 */
	public function getTime(): Carbon
	{
		return $this->timestamp;
	}

	/**
	 * Set the Time
	 *
	 * @param int $time
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setTime(int $time): APIRequestEvent
	{
		$this->timestamp = Carbon::createFromTimestampUTC($time);

		return $this;
	}

	/**
	 * Set the response time
	 *
	 * @param int $response_time_ms
	 *
	 * @return \Fuzz\Felk\Logging\APIRequestEvent
	 */
	public function setResponseTime(int $response_time_ms): APIRequestEvent
	{
		$this->response_time_ms = $response_time_ms;

		return $this;
	}

	/**
	 * Get the response time
	 *
	 * @return int
	 */
	public function getResponseTime(): int
	{
		return $this->response_time_ms;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [
			'timestamp'                  => $this->getTime()->toIso8601String(),
			'method'                     => $this->getRequest()->method(),
			'host'                       => $this->getRequest()->getHttpHost(),
			'route'                      => $this->getRoute(),
			'status_code'                => $this->getStatusCode(),
			'request_headers'            => json_encode($this->getRequestHeaders()),
			'request_body'               => $this->getRequest()->getContent(),
			'response_headers'           => json_encode($this->getResponseHeaders()),
			'response_body'              => $this->getResponse()->getContent(),
			'ip'                         => $this->getRequest()->ip(),
			'scheme'                     => $this->getRequest()->getScheme(),
			'port'                       => $this->getRequest()->getPort(),
			'environment'                => getenv('APP_ENV') ?? LoggableEvent::DEFAULT_ENVIRONMENT,
			'response_time_milliseconds' => $this->getResponseTime(),
		];
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 *
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Generate a unique ID for this event
	 *
	 * @return string
	 */
	public function getUniqueId(): string
	{
		return is_null($this->request_id) ? hash('sha256', $this->getRoute() . round(microtime(true) * 1000)) : $this->request_id;
	}

	/**
	 * Get the instance as a safe array with sensitive data removed
	 *
	 * @return array
	 */
	public function toSafeArray(): array
	{
		// We ignore the request/response to avoid having to redact potential PII
		$request_headers = $this->getRequestHeaders();

		foreach (self::UNSAFE_HEADERS as $header) {
			unset($request_headers[$header]);
		}

		return [
			'timestamp'                  => $this->getTime()->toIso8601String(),
			'method'                     => $this->getRequest()->method(),
			'host'                       => $this->getRequest()->getHttpHost(),
			'route'                      => $this->getRoute(),
			'status_code'                => $this->getStatusCode(),
			'request_headers'            => json_encode($request_headers),
			'response_headers'           => json_encode($this->getResponseHeaders()),
			'ip'                         => $this->getRequest()->ip(),
			'scheme'                     => $this->getRequest()->getScheme(),
			'port'                       => $this->getRequest()->getPort(),
			'environment'                => getenv('APP_ENV') ?? LoggableEvent::DEFAULT_ENVIRONMENT,
			'response_time_milliseconds' => $this->getResponseTime(),
		];
	}
}
