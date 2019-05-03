<?php
/**
 * clp-php-sdk
 * IncidentsAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-05-03, 11:05
 */

namespace Tests\Integration;


use Carbon\Carbon;
use Tests\CLPTestCase;

class IncidentsAPITest extends CLPTestCase {

	public function test_can_get_incidents() {
		$incidents = $this->client->incidents()->fetchIncidents();

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(100, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());
	}

	public function test_can_get_all_incidents() {
		$incidents = $this->client->incidents()->fetchAllIncidents([], 1000);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(1000, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

	}

	public function test_can_get_uneven_number_of_incidents() {
		$incidents = $this->client->incidents()->fetchAllIncidents([], 456);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(456, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());
	}

	public function test_can_filter_by_collection() {

		$collectionID = $this->config->get('clp.test.collection_id');

		$incidents = $this->client->incidents()->fetchAllIncidents(["collection_id eq '{$collectionID}'"], 150);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(150, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($collectionID) {

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});


	}

	public function test_can_filter_by_date() {

		$cutoffDate = Carbon::parse('2019-04-25 12:00:00');

		$incidents = $this->client->incidents()->fetchIncidentsAfterTimestamp($cutoffDate, 150, []);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(150, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Incident */

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->lte($cutoffDate));

		});

	}

	public function test_can_filter_by_date_and_collection() {

		$cutoffDate = Carbon::parse('2019-04-25 12:00:00');
		$collectionID = $this->config->get('clp.test.collection_id');

		$incidents = $this->client->incidents()->fetchIncidentsAfterTimestamp($cutoffDate, 256, ["collection_id eq '{$collectionID}'"]);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(256, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($cutoffDate, $collectionID) { /* @var $entry \Clay\CLP\Structs\Incident */

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->lte($cutoffDate));
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});

	}

}