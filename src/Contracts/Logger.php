<?php

namespace Fuzz\Felk\Contracts;

/**
 * Interface Logger
 *
 * A Logger accepts a LoggableEvent and writes it to its store
 *
 * @package Fuzz\Felk\Contracts
 */
interface Logger
{
	/**
	 * Log an event to the store
	 *
	 * @param \Fuzz\Felk\Contracts\LoggableEvent $event
	 * @param bool                               $force_safe
	 *
	 * @return array
	 */
	public function write(LoggableEvent $event, bool $force_safe = true): array;
}