<?php
/**
 * clp-php-sdk
 * CollectionAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 11:38
 */

namespace Tests\Integration;


use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Structs\NewAccessor;
use Clay\CLP\Structs\NewCollection;
use Tests\CLPTestCase;

class CollectionAPITest extends CLPTestCase {

	public function test_can_get_list_of_collections() {

		$collections = $this->clp->collections()->getCollections();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $collections);
		$this->assertGreaterThan(0, $collections->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $collections->items()->first());

	}

	public function test_can_get_single_collection() {

		$knownCollection = $this->clp->collections()->getCollections()->items()->first();

		$collection = $this->clp->collections()->getCollection($knownCollection->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $collection);
		$this->assertEquals($knownCollection->getID(), $collection->getID());
	}

	public function test_can_create_update_and_delete_collection() {

		$newCollection = new NewCollection(null, 'NL', null, null);

		$createdCollection = $this->clp->collections()->createCollection($newCollection);

		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $createdCollection);
		$this->assertNotNull($createdCollection->getID());
		$this->assertNull($createdCollection->getCustomerReference());

		$newCollection->customer_reference = "Test Customer Reference";

		$updatedCollection = $this->clp->collections()->updateCollection($createdCollection->getID(), $newCollection);

		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $updatedCollection);
		$this->assertNotNull($updatedCollection->getID());
		$this->assertNotNull($updatedCollection->getCustomerReference());

		$refreshedCollection = $this->clp->collections()->getCollection($updatedCollection->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $refreshedCollection);
		$this->assertEquals($updatedCollection->getID(), $refreshedCollection->getID());
		$this->assertEquals($updatedCollection->getCustomerReference(), $refreshedCollection->getCustomerReference());

		$this->clp->collections()->deleteCollection($refreshedCollection->getID());

		$remainingCollection = $this->clp->collections()->getCollection($updatedCollection->getID());

		$this->assertNull($remainingCollection);

	}

	public function test_can_get_accessor_settings_for_collection() {

		$this->markTestSkipped('Accessor settings API is not ready for testing');

		return;

		$newCollection = new NewCollection(null, 'NL', null, null);

		$createdCollection = $this->clp->collections()->createCollection($newCollection);
		$this->assertInstanceOf('Clay\CLP\Structs\Collection', $createdCollection);

		print_r($createdCollection);

		$newAccessor = new NewAccessor($createdCollection->getID());

		$createdAccessor = $this->clp->accessors()->createAccessor($newAccessor);
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $createdAccessor);

		print_r($createdAccessor);

		$collectionAccessors = $this->clp->collections()->getAccessorSettings($createdCollection->getID());

		print_r($collectionAccessors->items());

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $collectionAccessors);
		$this->assertGreaterThan(0, $collectionAccessors->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $collectionAccessors->items()->first());

		$this->assertEquals($createdAccessor->getID(), $collectionAccessors->items()->first()->getID());
		$this->assertEquals($createdCollection->getID(), $collectionAccessors->items()->first()->getCollectionID());

	}

}