<?php

namespace Fuzz\Felk\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Interface LoggableEvent
 *
 * A LoggableEvent is an event that can be written to a Logger
 *
 * @package Fuzz\Felk\Contracts
 */
interface LoggableEvent extends Jsonable, Arrayable
{
	/**
	 * Default token for environment
	 *
	 * @const string
	 */
	const DEFAULT_ENVIRONMENT = 'no_env_configured';

	/**
	 * Generate a unique ID for this event
	 *
	 * @return string
	 */
	public function getUniqueId(): string;

	/**
	 * Get the instance as a safe array with sensitive data removed
	 *
	 * @return array
	 */
	public function toSafeArray(): array;
}