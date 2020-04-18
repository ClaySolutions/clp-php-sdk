<?php
/**
 * clp-php-sdk
 * IdentityServerClientTest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:43
 */

namespace Tests\Integration;


use Clay\CLP\Clients\IdentityServerService;
use Tests\TestCase;

class IdentityServerClientTest extends TestCase {

	public function test_can_generate_an_access_token() {

		$client = new IdentityServerService($this->config);
		$accessToken = $client->fetchAccessToken();

		$this->assertInstanceOf('Clay\CLP\Structs\AccessToken', $accessToken);

		$this->assertIsString($accessToken->getAccessToken());
		$this->assertIsString($accessToken->getTokenType());
		$this->assertIsInt($accessToken->getExpiresIn());
		$this->assertFalse($accessToken->hasExpired());

	}

}