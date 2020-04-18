<?php
/**
 * clp-php-sdk
 * AccessorDevicesE2ETest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 16:54
 */

namespace Tests\Integration;


use Clay\CLP\Clients\VaultClient;
use Clay\CLP\Structs\Accessor;
use Clay\CLP\Structs\KeyPair;
use Clay\CLP\Structs\NewAccessor;
use Clay\CLP\Utilities\KeyPairGenerator;
use Tests\CLPTestCase;

class AccessorDevicesE2ETest extends CLPTestCase {

	/**
	 * @var string $vaultVariable
	 */
	private $vaultVariable;

	/**
	 * @var VaultClient $vault
	 */
	private $vault;

	/**
	 * @var KeyPair $keys
	 */
	private $keys;


	public function setUp() {
		parent::setUp();

		$this->vaultVariable = $this->config->get('vault.variable');
		$this->vault = new VaultClient($this->config);
		$this->keys = KeyPairGenerator::generate();
	}

	public function test_can_create_list_and_delete_device_for_accessor() {

		$accessor = $this->clp->accessors()->createAccessor(new NewAccessor());
		$device = $this->clp->accessors()->createDevice($accessor->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\AccessorDevice', $device);
		$this->assertNotNull($device->getID());

		$devices = $this->clp->accessors()->getDevices($accessor->getID());

		// TODO: why does a newly-created accessor has several devices?

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $devices);
		//$this->assertEquals(1, $devices->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\AccessorDevice', $devices->items()->first());
		//$this->assertEquals($device->getID(), $devices->items()->first()->getID());

		$this->clp->accessors()->deleteDevice($accessor->getID(), $device->getID());

		$remainingDevices = $this->clp->accessors()->getDevices($accessor->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $remainingDevices);
		//$this->assertEquals(0, $remainingDevices->items()->count());

		$this->clp->accessors()->deleteAccessor($accessor->getID());

	}

	public function test_can_generate_signed_certificate_for_accessor() {

		$accessor = $this->clp->accessors()->createAccessor(new NewAccessor());
		$device = $this->clp->accessors()->createDevice($accessor->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\AccessorDevice', $device);
		$this->assertNotNull($device->getID());

		$expiryDate = '2050-01-01';

		$unsignedCertificate = $this->clp->accessors()->createDeviceCertificate($accessor->getID(), $device->getID(), $this->keys->getPublicKey(), $expiryDate);

		$this->assertIsString($unsignedCertificate);
		$this->assertGreaterThan(0, strlen($unsignedCertificate));

		$signature = $this->vault->sign($this->vaultVariable, $unsignedCertificate, true);

		$this->assertIsString($signature);
		$this->assertEquals(105, strlen($signature));

		$isSignatureValid = $this->vault->verify($this->vaultVariable, $unsignedCertificate, $signature, true);
		$this->assertTrue($isSignatureValid);

		$signedCertificate = $this->clp->accessors()->activateDeviceCertificate($accessor->getID(), $device->getID(), $signature);

		$this->assertIsString($signedCertificate);
		$this->assertGreaterThan(0, strlen($signedCertificate));

	}

	public function test_can_get_mkey_for_an_iq() {

		$accessor = $this->clp->accessors()->createAccessor(new NewAccessor());
		$device = $this->clp->accessors()->createDevice($accessor->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\AccessorDevice', $device);
		$this->assertNotNull($device->getID());

		$expiryDate = '2050-01-01T12:30:00';

		$unsignedCertificate = $this->clp->accessors()->createDeviceCertificate($accessor->getID(), $device->getID(), $this->keys->getPublicKey(), $expiryDate);

		$this->assertIsString($unsignedCertificate);
		$this->assertGreaterThan(0, strlen($unsignedCertificate));

		$signature = $this->vault->sign($this->vaultVariable, $unsignedCertificate, true);

		$this->assertIsString($signature);
		$this->assertEquals(105, strlen($signature));

		$signedCertificate = $this->clp->accessors()->activateDeviceCertificate($accessor->getID(), $device->getID(), $signature);

		$this->assertIsString($signedCertificate);
		$this->assertGreaterThan(0, strlen($signedCertificate));

		$iq = $this->clp->iqs()->getIQs(["state eq 'active'", "online eq true"])->items()->first(); /* @var $iq \Clay\CLP\Structs\IQ */

		$mkey = $this->clp->accessors()->getDeviceMobileKey($accessor->getID(), $device->getID(), $iq->getID());

		$this->assertIsString($mkey);
		$this->assertGreaterThan(0, strlen($mkey));

	}

}