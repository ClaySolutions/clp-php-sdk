<?php
/**
 * clp-php-sdk
 * TestConfigurationLoadingTest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:22
 */

namespace Tests\Unit;

use Tests\TestCase;

class TestConfigurationLoadingTest extends TestCase {

	public function test_can_load_expected_env_variables_as_configuration() {

		$this->assertIsString($this->config->get('clp.endpoints.api'));
		$this->assertIsString($this->config->get('clp.endpoints.identity_server'));
		$this->assertIsString($this->config->get('clp.api_version'));
		$this->assertIsString($this->config->get('clp.client_id'));
		$this->assertIsString($this->config->get('clp.client_secret'));

	}

}