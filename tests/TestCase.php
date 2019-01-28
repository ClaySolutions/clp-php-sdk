<?php
/**
 * clp-php-sdk
 * TestCase.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:19
 */

namespace Tests;

use Clay\CLP\Utilities\DotEnvConfigLoader;
use Dotenv\Dotenv;

class TestCase extends \PHPUnit\Framework\TestCase {

	/**
	 * The configuration for tests loaded from the .env file
	 * @var DotEnvConfigLoader $config
	 */
	protected $config;

	protected function setUp() {
		parent::setUp();
		$this->config = new DotEnvConfigLoader(Dotenv::create(__DIR__));
	}

}