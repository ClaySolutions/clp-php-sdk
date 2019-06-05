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

use Clay\CLP\Structs\IQ;
use Clay\CLP\Structs\NewIQRegistration;
use Tests\CLPTestCase;

class IQAPITest extends CLPTestCase {

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

	public function test_can_get_iq_network_details() {
		$existingIQs = $this->client->iqs()->getIQs();
		$iq = $this->client->iqs()->getIQ($existingIQs->items()->first()->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\IQ', $iq);

		$networkDetails = $this->client->iqs()->getIQNetworkDetails($iq->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\IQNetworkDetails', $networkDetails);
		$this->assertInstanceOf('Illuminate\Support\Collection', $networkDetails->getNetworkAdapters());
		$this->assertInstanceOf('Clay\CLP\Structs\IQNetworkAdapter', $networkDetails->getNetworkAdapters()->first());

		$this->assertEquals(3, $networkDetails->getNetworkAdapters()->count());
		$this->assertTrue($networkDetails->getNetworkAdapters()->contains('status', 'active'));

		$this->assertTrue($networkDetails->getNetworkAdapters()->contains('type', 'm2m'));
		$this->assertTrue($networkDetails->getNetworkAdapters()->contains('type', 'ethernet'));
		$this->assertTrue($networkDetails->getNetworkAdapters()->contains('type', 'wifi'));

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

	public function test_can_set_iq_hardware_tree() {

		$this->markTestSkipped("IQ Set Tree: not ready to test (need Lock->detach first)");

	}

}