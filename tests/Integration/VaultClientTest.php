<?php
/**
 * clp-php-sdk
 * VaultClientTest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 16:05
 */

namespace Tests\Integration;


use Clay\CLP\Clients\VaultClient;
use Illuminate\Support\Str;
use Tests\TestCase;

class VaultClientTest extends TestCase {

	/**
	 * @var VaultClient $client
	 */
	private $client;

	public function setUp() {
		parent::setUp();

		$this->client = new VaultClient($this->config);
	}

	public function test_can_generate_vault_access_token() {

		$accessToken = $this->client->provideVaultToken();

		$this->assertIsString($accessToken->getAccessToken());
		$this->assertEquals(36, strlen($accessToken->getAccessToken()));
		$this->assertFalse($accessToken->hasExpired());

	}

	public function test_can_sign_variable_on_vault() {

		for($attempts = 0; $attempts < 3; $attempts++) {
			$randomString = Str::random(rand(1024, 2048));

			$variable = $this->config->get('vault.variable');
			$signature = $this->client->sign($variable, $randomString);

			$this->assertIsString($signature);
			$this->assertEquals(105, strlen($signature));
		}

	}

	public function test_can_verify_signature_on_vault() {

		$randomString = Str::random(rand(1024, 2048));
		$anotherRandomString = Str::random(rand(1024, 2048));

		$variable = $this->config->get('vault.variable');
		$signature = $this->client->sign($variable, $randomString);
		$anotherSignature = $this->client->sign($variable, $anotherRandomString);

		$this->assertIsString($signature);
		$this->assertEquals(105, strlen($signature));


		$isValid = $this->client->verify($variable, $randomString, $signature);

		$this->assertTrue($isValid);

		$invalidDifferentPayload = $this->client->verify($variable, $anotherRandomString, $signature);

		$this->assertFalse($invalidDifferentPayload);

		$invalidDifferentSignature = $this->client->verify($variable, $randomString, $anotherSignature);

		$this->assertFalse($invalidDifferentSignature);

	}

}