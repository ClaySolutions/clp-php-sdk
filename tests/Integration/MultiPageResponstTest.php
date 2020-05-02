<?php
declare(strict_types=1);
namespace Tests\Integration;

use Clay\CLP\Structs\Accessor;
use Clay\CLP\Structs\APIRequest;
use Clay\CLP\Utilities\MultiPageResponse;
use Tests\CLPTestCase;

final class MultiPageResponstTest extends CLPTestCase {

	public function testCanFetchFullMultiPageCollection(): void {

		$request = new APIRequest('accessors');
		$results = MultiPageResponse::fetchFullCollection($request, $this->client, 128, Accessor::class);

		$this->assertEquals(128, $results->count());

		$foundAccessorIDs = $results->map(function (Accessor $accessor) {
			$this->assertNotNull($accessor->getID());

			return $accessor->getID();
		});

		$this->assertEquals(
			$foundAccessorIDs->unique()->count(),
			$foundAccessorIDs->count(),
			'Found duplicate Accessor IDs in the resulting collection!'
		);

	}

}