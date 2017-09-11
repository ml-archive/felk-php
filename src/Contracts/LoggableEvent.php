<?php

namespace Fuzz\Felk\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

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
}