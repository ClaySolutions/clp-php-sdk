<?php
/**
 * clp-php-sdk
 * LockAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-30, 15:21
 */

namespace Tests\Integration;


use Carbon\Carbon;
use Clay\CLP\Structs\EasyOfficeModeSchedule;
use Tests\CLPTestCase;

class LockAPITest extends CLPTestCase {

	public function test_can_get_list_of_locks() {

		$locks = $this->client->locks()->getLocks();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $locks);
		$this->assertInstanceOf('Illuminate\Support\Collection', $locks->items());
		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $locks->items()->first());

	}

	public function test_can_get_single_lock() {
		$existingLocks = $this->client->locks()->getLocks();
		$lock = $this->client->locks()->getLock($existingLocks->items()->first()->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $lock);
	}

	public function test_can_find_lock_by_mac_address() {
		$macAddress = $this->config->get('clp.test.lock.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$locks = $this->client->locks()->getLocks(["mac eq '{$macAddress}'"]);

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $locks);
		$this->assertGreaterThan(0, $locks->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $locks->items()->first());

	}

	public function test_can_search_for_online_and_locked_locks() {

		$locks = $this->client->locks()->getLocks(["locked_state eq 'locked'", "online eq true"]);

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $locks);
		$this->assertGreaterThan(0, $locks->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $locks->items()->first());

	}

	public function test_can_get_offline_keys_for_lock() {
		$macAddress = $this->config->get('clp.test.lock.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$lock = $this->client->locks()->getLocks(["mac eq '{$macAddress}'"])->items()->first(); /* @var $lock \Clay\CLP\Structs\Lock */

		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $lock);

		$offlineKeys = $this->client->locks()->getOfflineKeys($lock->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $offlineKeys);
		$this->assertGreaterThan(0, $offlineKeys->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Key', $offlineKeys->items()->first());

	}

	public function test_can_add_and_remove_offline_keys_for_lock() {
		$macAddress = $this->config->get('clp.test.lock.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$lock = $this->client->locks()->getLocks(["mac eq '{$macAddress}'"])->items()->first(); /* @var $lock \Clay\CLP\Structs\Lock */

		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $lock);

		$offlineKeys = $this->client->locks()->getOfflineKeys($lock->getID());

		// Ensures we have at least one key
		$this->assertGreaterThan(0, $offlineKeys->items()->count());

		$numKeysBeforeRemoving = $offlineKeys->items()->count();

		// Pick an existing, known key
		$existingKey = $offlineKeys->items()->first();

		// Remove a known key from the lock
		$this->client->locks()->removeOfflineKey($lock->getID(), $existingKey->getID());

		// Ensures the key was correctly removed
		$updatedOfflineKeys = $this->client->locks()->getOfflineKeys($lock->getID());
		$this->assertEquals($numKeysBeforeRemoving - 1, $updatedOfflineKeys->items()->count());

		// Add back the known key
		$this->client->locks()->addOfflineKey($lock->getID(), $existingKey->getID());

		// Check again if the key was added
		$updatedOfflineKeys = $this->client->locks()->getOfflineKeys($lock->getID());
		$this->assertEquals($numKeysBeforeRemoving, $updatedOfflineKeys->items()->count());

	}

	public function test_can_add_update_and_delete_eom_schedule_for_lock() {

		$macAddress = $this->config->get('clp.test.lock.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$lock = $this->client->locks()->getLocks(["mac eq '{$macAddress}'"])->items()->first(); /* @var $lock \Clay\CLP\Structs\Lock */

		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $lock);

		$this->client->locks()->deleteEasyOfficeModeSchedule($lock->getID());

		$existingEOMSchedule = $this->client->locks()->getEasyOfficeModeSchedule($lock->getID());

		$this->assertNull($existingEOMSchedule);

		$yesterday = Carbon::yesterday()->toDateString();
		$tomorrow = Carbon::tomorrow()->toDateString();

		$newEOMSchedule = new EasyOfficeModeSchedule();
		$newEOMSchedule->setMondayEnabled(true);
		$newEOMSchedule->setWednesdayEnabled(true);
		$newEOMSchedule->setStartTime('09:12');
		$newEOMSchedule->setEndTime('22:56');
		$newEOMSchedule->setStartDate($yesterday);
		$newEOMSchedule->setEndDate($tomorrow);

		$this->client->locks()->setEasyOfficeModeSchedule($lock->getID(), $newEOMSchedule);

		$updatedEOMSchedule = $this->client->locks()->getEasyOfficeModeSchedule($lock->getID());

		$this->assertTrue($updatedEOMSchedule->isMondayEnabled());
		$this->assertTrue($updatedEOMSchedule->isWednesdayEnabled());
		$this->assertFalse($updatedEOMSchedule->isTuesdayEnabled());
		$this->assertFalse($updatedEOMSchedule->isThursdayEnabled());
		$this->assertFalse($updatedEOMSchedule->isFridayEnabled());
		$this->assertFalse($updatedEOMSchedule->isSaturdayEnabled());
		$this->assertFalse($updatedEOMSchedule->isSundayEnabled());

		$this->assertEquals('09:12:00', $updatedEOMSchedule->getStartTime());
		$this->assertEquals('22:56:59', $updatedEOMSchedule->getEndTime());

		$this->assertEquals($yesterday, $updatedEOMSchedule->getStartDate());
		$this->assertEquals($tomorrow, $updatedEOMSchedule->getEndDate());

		$this->client->locks()->deleteEasyOfficeModeSchedule($lock->getID());

		$deletedEOMSchedule = $this->client->locks()->getEasyOfficeModeSchedule($lock->getID());

		$this->assertNull($deletedEOMSchedule);

	}

	public function test_can_start_tag_registration_mode_on_a_lock() {

		$macAddress = $this->config->get('clp.test.lock.mac');

		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$lock = $this->client->locks()->getLocks(["mac eq '{$macAddress}'"])->items()->first(); /* @var $lock \Clay\CLP\Structs\Lock */

		$this->client->locks()->triggerLockRegistrationMode($lock->getID(), 15);

		// TODO: why is this 404?

	}

}