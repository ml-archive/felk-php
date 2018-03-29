<?php

namespace Fuzz\Felk\Logging;

use Carbon\Carbon;
use Fuzz\Felk\Contracts\LoggableEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
	 *
	 * @todo we should make toArray the default "safe" way to format the data, and instead make a toRawArray() which won't process the request/response.
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
			'request_headers'            => json_encode($this->maskedRequestHeaders()),
			'request_body'               => json_encode($this->maskedRequestContent()),
			'response_headers'           => json_encode($this->getResponseHeaders()),
			'response_body'              => $this->maskedResponseContent(),
			'ip'                         => $this->getRequest()->ip(),
			'scheme'                     => $this->getRequest()->getScheme(),
			'port'                       => $this->getRequest()->getPort(),
			'environment'                => getenv('APP_ENV') ?? LoggableEvent::DEFAULT_ENVIRONMENT,
			'response_time_milliseconds' => $this->getResponseTime(),
		];
	}

	/**
	 * Mask sensitive data from the request headers and return it as an array.
	 *
	 * @return array
	 */
	private function maskedRequestHeaders(): array
	{
		return $this->mask($this->getRequestHeaders(), config('felk.mask_headers'));
	}

	/**
	 * Mask sensitive data from the response headers and return it as an array.
	 *
	 * @return array
	 */
	private function maskedResponseHeaders(): array
	{
		return $this->mask($this->getResponseHeaders(), config('felk.mask_headers'));
	}

	/**
	 * Mask sensitive data from the request content and return it as an array.
	 *
	 * @return array
	 */
	private function maskedRequestContent(): array
	{
		return $this->mask($this->getRequest()->all(), config('felk.mask_input'));
	}

	/**
	 * Mask sensitive data from the response content and return it as a string.
	 *
	 * @return string
	 *
	 * @TODO this should be consistent with the return type of other masking functions.
	 */
	private function maskedResponseContent(): string
	{
		$content = $this->getResponse()->getContent();

		if ($this->getResponse() instanceof JsonResponse) {
			$content = $this->mask($this->getResponse()->getData(true), config('felk.mask_input'));

			$content = json_encode($content, $this->getResponse()->getEncodingOptions());
		}

		return $content;
	}

	/**
	 * Run through an array and mask the values of any found keys.
	 *
	 * @param array  $array - The array to search for keys.
	 * @param array  $keys - The keys to mask.
	 * @param string $mask - The value to replace the keys with.
	 *
	 * @return array
	 */
	private function mask(array $array, array $keys, string $mask = 'MASKED')
	{
		$loweredKeysArr = array_change_key_case($array);

		foreach ($keys as $key) {
			if (array_key_exists(strtolower($key), $loweredKeysArr)) {
				$array[$key] = $mask;
			}
		}

		return $array;
	}
}
