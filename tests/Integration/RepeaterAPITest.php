<?php
/**
 * clp-php-sdk
 * RepeaterAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:39
 */

namespace Tests\Integration;


use Tests\CLPTestCase;

class RepeaterAPITest extends CLPTestCase {

	public function test_can_get_list_of_repeaters() {

		$repeaters = $this->client->repeaters()->getRepeaters();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $repeaters);
		$this->assertGreaterThan(0, $repeaters->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Repeater', $repeaters->items()->first());

	}

	public function test_can_get_single_repeater() {

		$existingRepeaters = $this->client->repeaters()->getRepeaters();
		$repeater = $this->client->repeaters()->getRepeater($existingRepeaters->items()->first()->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Repeater', $repeater);
		$this->assertEquals($existingRepeaters->items()->first()->getID(), $repeater->getID());

	}

}