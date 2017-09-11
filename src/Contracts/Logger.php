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
	 *
	 * @return array
	 */
	public function write(LoggableEvent $event): array;
}