<?php

namespace Fuzz\Felk\Tests;

use ErrorException;
use Mockery;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * Set up our tests
	 */
	public function setUp()
	{
		parent::setUp();

		set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
			// error was suppressed with the @-operator
			if (0 === error_reporting()) {
				return false;
			}
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});

		date_default_timezone_set('UTC');
	}

	/**
	 * Add mockery expectation counts
	 *
	 * @throws \Throwable
	 */
	public function tearDown()
	{
		if (class_exists('Mockery')) {
			parent::verifyMockObjects();

			if ($container = Mockery::getContainer()) {
				$this->addToAssertionCount($container->mockery_getExpectationCount());
			}

			Mockery::close();
		}

		parent::tearDown();
	}
}