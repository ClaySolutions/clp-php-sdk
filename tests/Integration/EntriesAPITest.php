<?php
/**
 * clp-php-sdk
 * EntriesAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-05-02, 13:51
 */

namespace Tests\Integration;


use Carbon\Carbon;
use Tests\CLPTestCase;

class EntriesAPITest extends CLPTestCase {

	public function test_can_get_entries() {
		$entries = $this->client->entries()->fetchEntries();

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(100, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());
	}

	public function test_can_get_all_entries() {
		$entries = $this->client->entries()->fetchAllEntries([], 1000);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(1000, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

	}

	public function test_can_get_uneven_number_of_entries() {
		$entries = $this->client->entries()->fetchAllEntries([], 456);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(456, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());
	}

	public function test_can_filter_by_collection() {

		$collectionID = $this->config->get('clp.test.collection_id');

		$entries = $this->client->entries()->fetchAllEntries(["collection_id eq '{$collectionID}'"], 150);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(150, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$entries->each(function ($entry) use ($collectionID) {

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});


	}

	public function test_can_filter_by_date() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');

		$entries = $this->client->entries()->fetchEntriesAfterTimestamp($cutoffDate, 150, []);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(150, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$entries->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));

		});

	}

	public function test_can_filter_by_date_and_collection() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');
		$collectionID = $this->config->get('clp.test.collection_id');

		$entries = $this->client->entries()->fetchEntriesAfterTimestamp($cutoffDate, 256, ["collection_id eq '{$collectionID}'"]);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(256, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$entries->each(function ($entry) use ($cutoffDate, $collectionID) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});

	}

	public function test_has_recent_events() {

		$cutoffDate = Carbon::parse('2019-04-01 00:00:00');

		$entries = $this->client->entries()->fetchEntriesAfterTimestamp($cutoffDate, 150, []);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertGreaterThan(0, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$entries->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));

		});

	}

}