<?php
/**
 * clp-php-sdk
 * AccessGroupAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 13:49
 */

namespace Tests\Integration;


use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Structs\NewAccessGroup;
use Clay\CLP\Structs\NewAccessor;
use Clay\CLP\Structs\TimeSchedule;
use Tests\CLPTestCase;

class AccessGroupAPITest extends CLPTestCase {

	public function test_can_get_list_of_groups() {
		$groups = $this->clp->accessGroups()->getGroups();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $groups);
		$this->assertGreaterThan(0, $groups->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $groups->items()->first());

	}

	public function test_can_get_single_group() {

		$knownGroup = $this->clp->accessGroups()->getGroups()->items()->first();
		$group = $this->clp->accessGroups()->getGroup($knownGroup->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $group);
		$this->assertEquals($group->getID(), $knownGroup->getID());

	}

	public function test_can_create_update_and_delete_a_group() {

		$newGroup = new NewAccessGroup();
		$createdGroup = $this->clp->accessGroups()->createGroup($newGroup);

		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $createdGroup);
		$this->assertNull($createdGroup->getCustomerReference());

		$newGroup->customer_reference = "Test Reference";

		$updatedGroup = $this->clp->accessGroups()->updateGroup($createdGroup->getID(), $newGroup);

		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $updatedGroup);
		$this->assertEquals($newGroup->customer_reference, $updatedGroup->getCustomerReference());

		$refreshedGroup = $this->clp->accessGroups()->getGroup($updatedGroup->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $refreshedGroup);
		$this->assertEquals($newGroup->customer_reference, $refreshedGroup->getCustomerReference());

		$this->clp->accessGroups()->deleteGroup($refreshedGroup->getID());

		$this->expectException(EndpointNotFound::class);

		$remainingGroup = $this->clp->accessGroups()->getGroup($updatedGroup->getID());

		$this->assertNull($remainingGroup);

	}

	public function test_can_get_add_and_remove_time_schedules() {

		$startDate = '2019-01-01';
		$endDate = '2019-12-01';
		$startTime = '12:34:00';
		$endTime = '23:45:59';

		$newSchedule = new TimeSchedule();
		$newSchedule->setStartDate($startDate);
		$newSchedule->setEndDate($endDate);
		$newSchedule->setStartTime($startTime);
		$newSchedule->setEndTime($endTime);
		$newSchedule->setMondayEnabled(true);
		$newSchedule->setTuesdayEnabled(true);
		$newSchedule->setWednesdayEnabled(true);
		$newSchedule->setThursdayEnabled(true);
		$newSchedule->setFridayEnabled(true);

		$newGroup = new NewAccessGroup();
		$createdGroup = $this->clp->accessGroups()->createGroup($newGroup);

		$createdSchedule = $this->clp->accessGroups()->addTimeSchedule($createdGroup->getID(), $newSchedule);
		$this->assertInstanceOf('Clay\CLP\Structs\TimeSchedule', $createdSchedule);
		$this->assertEquals($startDate, $createdSchedule->getStartDate());
		$this->assertEquals($endDate, $createdSchedule->getEndDate());
		$this->assertEquals($startTime, $createdSchedule->getStartTime());
		$this->assertEquals($endTime, $createdSchedule->getEndTime());
		$this->assertFalse($createdSchedule->isSundayEnabled());
		$this->assertFalse($createdSchedule->isSaturdayEnabled());
		$this->assertTrue($createdSchedule->isMondayEnabled());
		$this->assertTrue($createdSchedule->isTuesdayEnabled());
		$this->assertTrue($createdSchedule->isWednesdayEnabled());
		$this->assertTrue($createdSchedule->isThursdayEnabled());
		$this->assertTrue($createdSchedule->isFridayEnabled());

		$foundSchedules = $this->clp->accessGroups()->getTimeSchedules($createdGroup->getID());
		$foundSchedule = $foundSchedules->items()->first();

		$this->assertEquals(1, $foundSchedules->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\TimeSchedule', $foundSchedule);

		$this->assertEquals($startDate, $foundSchedule->getStartDate());
		$this->assertEquals($endDate, $foundSchedule->getEndDate());
		$this->assertEquals($startTime, $foundSchedule->getStartTime());
		$this->assertEquals($endTime, $foundSchedule->getEndTime());
		$this->assertFalse($foundSchedule->isSundayEnabled());
		$this->assertFalse($foundSchedule->isSaturdayEnabled());
		$this->assertTrue($foundSchedule->isMondayEnabled());
		$this->assertTrue($foundSchedule->isTuesdayEnabled());
		$this->assertTrue($foundSchedule->isWednesdayEnabled());
		$this->assertTrue($foundSchedule->isThursdayEnabled());
		$this->assertTrue($foundSchedule->isFridayEnabled());

		$newSchedule->setFridayEnabled(false);
		$newSchedule->setSundayEnabled(true);
		$newSchedule->setStartTime('09:00');

		$updatedSchedule = $this->clp->accessGroups()->updateTimeSchedule($createdGroup->getID(), $foundSchedule->getID(), $newSchedule);

		$this->assertEquals($startDate, $updatedSchedule->getStartDate());
		$this->assertEquals($endDate, $updatedSchedule->getEndDate());
		$this->assertEquals('09:00:00', $updatedSchedule->getStartTime());
		$this->assertEquals($endTime, $updatedSchedule->getEndTime());
		$this->assertTrue($updatedSchedule->isSundayEnabled());
		$this->assertFalse($updatedSchedule->isSaturdayEnabled());
		$this->assertTrue($updatedSchedule->isMondayEnabled());
		$this->assertTrue($updatedSchedule->isTuesdayEnabled());
		$this->assertTrue($updatedSchedule->isWednesdayEnabled());
		$this->assertTrue($updatedSchedule->isThursdayEnabled());
		$this->assertFalse($updatedSchedule->isFridayEnabled());

		$this->clp->accessGroups()->removeTimeSchedule($createdGroup->getID(), $updatedSchedule->getID());

		$refreshedSchedules = $this->clp->accessGroups()->getTimeSchedules($createdGroup->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $refreshedSchedules);
		$this->assertEquals(0, $refreshedSchedules->items()->count());

		$this->clp->accessGroups()->deleteGroup($createdGroup->getID());

	}

	public function test_can_get_add_and_remove_locks() {

		$group = $this->clp->accessGroups()->createGroup(new NewAccessGroup());
		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $group);

		$macAddress = $this->config->get('clp.test.lock.mac');
		$this->assertIsString($macAddress, 'You have not configured a Test Lock MAC Address in your environment!');

		$lock = $this->clp->locks()->getLocks(["mac eq '{$macAddress}'"])->items()->first(); /* @var $lock \Clay\CLP\Structs\Lock */
		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $lock);

		$this->clp->accessGroups()->updateGroupLocks($group->getID(), [$lock->getID()]);

		$existingLocks = $this->clp->accessGroups()->getGroupLocks($group->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $existingLocks);
		$this->assertEquals(1, $existingLocks->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Lock', $existingLocks->items()->first());
		$this->assertEquals($lock->getID(), $existingLocks->items()->first()->getID());
		$this->assertEquals(strtolower($macAddress), strtolower($existingLocks->items()->first()->getMacAddress()));

		$this->clp->accessGroups()->updateGroupLocks($group->getID(), [], [$lock->getID()]);

		$remainingLocks = $this->clp->accessGroups()->getGroupLocks($group->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $remainingLocks);
		$this->assertEquals(0, $remainingLocks->items()->count());

	}

	public function test_can_get_add_and_remove_accessors() {

		$group = $this->clp->accessGroups()->createGroup(new NewAccessGroup());
		$this->assertInstanceOf('Clay\CLP\Structs\AccessGroup', $group);

		$accessor = $this->clp->accessors()->createAccessor(new NewAccessor());

		$this->clp->accessGroups()->updateGroupAccessors($group->getID(), [$accessor->getID()]);

		$existingAccessors = $this->clp->accessGroups()->getGroupAccessors($group->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $existingAccessors);
		$this->assertEquals(1, $existingAccessors->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $existingAccessors->items()->first());
		$this->assertEquals($accessor->getID(), $existingAccessors->items()->first()->getID());

		$this->clp->accessGroups()->updateGroupAccessors($group->getID(), [], [$accessor->getID()]);

		$remainingAccessors = $this->clp->accessGroups()->getGroupAccessors($group->getID());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $remainingAccessors);
		$this->assertEquals(0, $remainingAccessors->items()->count());

	}

}