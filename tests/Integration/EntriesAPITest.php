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
use Clay\CLP\Structs\Entry;
use Tests\CLPTestCase;

class EntriesAPITest extends CLPTestCase {

	private function assertIsValidEntry(Entry $entry) {
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);
		$this->assertNotNull($entry->collection_id);
		$this->assertNotNull($entry->event_category);
		$this->assertNotNull($entry->iq_id);
		$this->assertNotNull($entry->id);
		$this->assertNotNull($entry->utc_date_time);
		$this->assertNotNull($entry->local_date_time);

		$this->assertInstanceOf('Carbon\Carbon', Carbon::parse($entry->utc_date_time));
		$this->assertInstanceOf('Carbon\Carbon', Carbon::parse($entry->local_date_time));
	}

	public function test_can_get_entries() {
		$entries = $this->client->entries()->fetchEntries();

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertGreaterThan(0, $entries->count());

		$this->assertIsValidEntry($entries->first());
		$this->assertIsValidEntry($entries->last());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) {
			$this->assertIsValidEntry($entry);
		});
	}

	public function test_can_get_all_entries() {
		$entries = $this->client->entries()->fetchAllEntries([], 600);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(600, $entries->count());

		$this->assertIsValidEntry($entries->first());
		$this->assertIsValidEntry($entries->last());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) {
			$this->assertIsValidEntry($entry);
		});

	}

	public function test_can_use_continuation_token() {

		$entries = $this->client->entries()->fetchAllEntries([], 15);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(15, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$continuationToken = $entries->continuationToken ?? null;

		$this->assertNotNull($continuationToken);

		$nextEntries = $this->client->entries()->fetchAllEntries([], 15, $continuationToken);

		$this->assertInstanceOf('Illuminate\Support\Collection', $nextEntries);
		$this->assertEquals(15, $nextEntries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $nextEntries->first());

		$this->assertNotEquals($nextEntries->first()->getId(), $entries->first()->getId());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) {
			$this->assertIsValidEntry($entry);
		});

	}

	public function test_can_get_uneven_number_of_entries() {
		$entries = $this->client->entries()->fetchAllEntries([], 219);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(219, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) {
			$this->assertIsValidEntry($entry);
		});
	}

	public function test_can_filter_by_collection() {

		$collectionID = $this->config->get('clp.test.collection_id');

		$entries = $this->client->entries()->fetchAllEntries(["collection_id eq '{$collectionID}'"], 150);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(150, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) use ($collectionID) {

			$this->assertIsValidEntry($entry);
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});


	}

	public function test_can_filter_by_date() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');

		$entries = $this->client->entries()->fetchEntriesAfterTimestamp($cutoffDate, 150, []);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(150, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);
			$this->assertIsValidEntry($entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));

		});

	}

	public function test_can_filter_by_date_and_collection() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');
		$collectionID = $this->config->get('clp.test.collection_id');

		$entries = $this->client->entries()->fetchEntriesAfterTimestamp($cutoffDate, 128, ["collection_id eq '{$collectionID}'"]);

		$this->assertInstanceOf('Illuminate\Support\Collection', $entries);
		$this->assertEquals(128, $entries->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entries->first());

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) use ($cutoffDate, $collectionID) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);
			$this->assertIsValidEntry($entry);

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

		$this->assertEquals($entries->pluck('id')->unique()->count(), $entries->count());

		$entries->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Entry */

			$this->assertInstanceOf('Clay\CLP\Structs\Entry', $entry);
			$this->assertIsValidEntry($entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));

		});

	}

}