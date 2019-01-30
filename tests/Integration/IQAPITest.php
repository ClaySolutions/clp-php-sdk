<?php
/**
 * clp-php-sdk
 * IQAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:40
 */

namespace Tests\Integration;


use Clay\CLP\Clients\CLPClient;
use Clay\CLP\Clients\IdentityServerClient;
use Clay\CLP\Structs\IQ;
use Clay\CLP\Structs\NewIQRegistration;
use Tests\TestCase;

class IQAPITest extends TestCase {

	/**
	 * @var CLPClient $client
	 */
	protected $client;

	/**
	 * @var IdentityServerClient
	 */
	protected $identityServer;


	public function setUp() {
		parent::setUp();

		$this->identityServer = new IdentityServerClient($this->config);

		$this->client = new CLPClient($this->config);
		$this->client->setAuthorizationHeaderProvider(function () {
			return $this->identityServer->provideAccessToken()->generateAuthorizationHeader();
		});
	}

	public function test_can_get_iqs() {

		$iqs = $this->client->iqs()->getIQs();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $iqs);
		$this->assertInstanceOf('Illuminate\Support\Collection', $iqs->items());
		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iqs->items()->first());

	}

	public function test_can_get_single_iq() {
		$existingIQs = $this->client->iqs()->getIQs();
		$iq = $this->client->iqs()->getIQ($existingIQs->items()->first()->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iq);
	}

	public function test_can_find_iq_by_mac_address() {
		$macAddress = $this->config->get('clp.test.iq.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test IQ MAC Address in your environment!');

		$iqs = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"]);

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $iqs);
		$this->assertGreaterThan(0, $iqs->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iqs->items()->first());

	}

	public function test_can_search_for_online_active_iqs() {

		$iqs = $this->client->iqs()->getIQs(["state eq 'active'", "online eq true"]);

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $iqs);
		$this->assertGreaterThan(0, $iqs->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iqs->items()->first());

	}

	public function test_can_delete_existing_iq() {

		return $this->markTestSkipped('Delete IQ: Not ready for testing');

		$macAddress = $this->config->get('clp.test.iq.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test IQ MAC Address in your environment!');

		$existingIQ = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"])->items()->first(); /* @var $existingIQ IQ */

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $existingIQ);

		$this->client->iqs()->deleteIQ($existingIQ->getID());

		$remainingIqsWithSameMacAddress = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"]);

		$this->assertEquals(0, $remainingIqsWithSameMacAddress->items()->count());
		$this->assertEmpty($remainingIqsWithSameMacAddress->items());

	}

	public function test_can_register_iq() {

		return $this->markTestSkipped('Register IQ: Not ready for testing');

		$macAddress = $this->config->get('clp.test.iq.mac');
		$activationCode = $this->config->get('clp.test.iq.activation_code');
		$customerReference = 'test_sdk_' . uniqid();

		$this->assertIsString($macAddress, 'You have not configured a Test IQ MAC Address in your environment!');
		$this->assertIsString($activationCode, 'You have not configured a Test IQ Activation Code in your environment!');

		$existingIQ = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"])->items()->first(); /* @var $existingIQ IQ */

		if($existingIQ) {
			$this->markTestSkipped('Cannot test IQ registration: an IQ already exists with the given test MAC address');
		}

		$newIQ = $this->client->iqs()->registerIQ(new NewIQRegistration(
			$customerReference,
			null,
			'Europe/Amsterdam',
			$activationCode,
			true
		));

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $newIQ);
		$this->assertEquals($customerReference, $newIQ->getCustomerReference());

		$foundIQWithSameMAC = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"])->items()->first(); /* @var $foundIQWithSameMAC IQ */

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $foundIQWithSameMAC);
		$this->assertEquals($newIQ->getID(), $foundIQWithSameMAC->getID());
		$this->assertEquals($newIQ->getCustomerReference(), $foundIQWithSameMAC->getCustomerReference());

	}

	public function test_can_get_iq_hardware_tree() {

		$macAddress = $this->config->get('clp.test.iq.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test IQ MAC Address in your environment!');

		$iq = $this->client->iqs()->getIQs(["mac eq '{$macAddress}'"])->items()->first(); /* @var $iq \Clay\CLP\Structs\IQ */

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iq);

		$hardwareTree = $this->client->iqs()->getHardwareTree($iq->getID());

		$this->assertInstanceOf('Illuminate\Support\Collection', $hardwareTree);
		$this->assertGreaterThan(0, $hardwareTree->count());
		$this->assertInstanceOf('Clay\CLP\Structs\IQHardware', $hardwareTree->first());

	}

}