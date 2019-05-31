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
		$incidents = $this->client->incidents()->fetchAllIncidents([], 200);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(200, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

	}

	public function test_can_use_continuation_token() {

		$incidents = $this->client->incidents()->fetchAllIncidents([], 15);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(15, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$continuationToken = $incidents->continuationToken ?? null;
		$this->assertNotNull($continuationToken);

		$nextIncidents = $this->client->incidents()->fetchAllIncidents([], 15, $continuationToken);

		$this->assertInstanceOf('Illuminate\Support\Collection', $nextIncidents);
		$this->assertEquals(15, $nextIncidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $nextIncidents->first());

		$this->assertNotEquals($nextIncidents->first()->id, $incidents->first()->id);

	}

	public function test_can_get_uneven_number_of_incidents() {
		$incidents = $this->client->incidents()->fetchAllIncidents([], 128);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(128, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());
	}

	public function test_can_filter_by_collection() {

		$collectionID = $this->config->get('clp.test.collection_id');

		$incidents = $this->client->incidents()->fetchAllIncidents(["collection_id eq '{$collectionID}'"], 64);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(64, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($collectionID) {

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});


	}

	public function test_can_filter_by_date() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');

		$incidents = $this->client->incidents()->fetchIncidentsAfterTimestamp($cutoffDate, 64, []);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(64, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($cutoffDate) { /* @var $entry \Clay\CLP\Structs\Incident */

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));

		});

	}

	public function test_can_filter_by_date_and_collection() {

		$cutoffDate = Carbon::parse('2019-01-01 12:00:00');
		$collectionID = $this->config->get('clp.test.collection_id');

		$incidents = $this->client->incidents()->fetchIncidentsAfterTimestamp($cutoffDate, 64, ["collection_id eq '{$collectionID}'"]);

		$this->assertInstanceOf('Illuminate\Support\Collection', $incidents);
		$this->assertEquals(64, $incidents->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Incident', $incidents->first());

		$incidents->each(function ($entry) use ($cutoffDate, $collectionID) { /* @var $entry \Clay\CLP\Structs\Incident */

			$this->assertInstanceOf('Clay\CLP\Structs\Incident', $entry);

			$date = Carbon::parse($entry->getUtcDateTime());

			$this->assertInstanceOf('Carbon\Carbon', $date);
			$this->assertTrue($date->gte($cutoffDate));
			$this->assertEquals($collectionID, $entry->getCollectionID());

		});

	}

}