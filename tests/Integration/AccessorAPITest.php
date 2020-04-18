<?php
/**
 * clp-php-sdk
 * AccessorAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 10:42
 */

namespace Tests\Integration;


use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Structs\NewAccessor;
use Tests\CLPTestCase;

class AccessorAPITest extends CLPTestCase {

	public function test_can_get_list_of_accessors() {

		$accessors = $this->clp->accessors()->getAccessors();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $accessors);
		$this->assertGreaterThan(0, $accessors->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $accessors->items()->first());

	}

	public function test_can_get_single_accessor() {

		$knownAccessor = $this->clp->accessors()->getAccessors()->items()->first();

		$accessor = $this->clp->accessors()->getAccessor($knownAccessor->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $accessor);
		$this->assertEquals($accessor->getID(), $knownAccessor->getID());

	}

	public function test_can_create_update_and_delete_accessor() {

		$newAccessor = new NewAccessor(null, false);
		$createdAccessor = $this->clp->accessors()->createAccessor($newAccessor);

		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $createdAccessor);
		$this->assertFalse($createdAccessor->isBlocked());
		$this->assertNotNull($createdAccessor->getID());


		$newAccessor->blocked = true;
		$updatedAccessor = $this->clp->accessors()->updateAccessor($createdAccessor->getID(), $newAccessor);
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $updatedAccessor);
		$this->assertTrue($updatedAccessor->isBlocked());
		$this->assertNotNull($updatedAccessor->getID());

		$updatedAccessor = $this->clp->accessors()->getAccessor($updatedAccessor->getID());
		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $updatedAccessor);
		$this->assertTrue($updatedAccessor->isBlocked());
		$this->assertNotNull($updatedAccessor->getID());

		$this->clp->accessors()->deleteAccessor($updatedAccessor->getID());

		$this->expectException(EndpointNotFound::class);

		$deletedAccessor = $this->clp->accessors()->getAccessor($updatedAccessor->getID());

		$this->assertNull($deletedAccessor);

	}

	public function test_can_assign_and_unassign_tags_to_accessor() {

		$tagNumber = $this->config->get('clp.test.tag_number');
		$this->assertIsString($tagNumber, 'You have not configured a Test Tag Number in your environment!');

		$newAccessor = new NewAccessor(null, false);
		$createdAccessor = $this->clp->accessors()->createAccessor($newAccessor);

		$this->assertInstanceOf('Clay\CLP\Structs\Accessor', $createdAccessor);
		$this->assertFalse($createdAccessor->isBlocked());
		$this->assertNotNull($createdAccessor->getID());

		$tag = $this->clp->tags()->getTagByNumber($tagNumber);
		$this->assertInstanceOf('Clay\CLP\Structs\Tag', $tag);

		$this->clp->accessors()->assignTag($createdAccessor->getID(), $tag->getID());

		$tagKey = $this->clp->accessors()->getAssignedKeyByTagID($createdAccessor->getID(), $tag->getID());
		$tagKeyByNumber = $this->clp->accessors()->getAssignedKeyByTagNumber($createdAccessor->getID(), $tag->getTagNumber());

		$this->assertNotNull($tagKey);
		$this->assertNotNull($tagKeyByNumber);

		$this->clp->accessors()->removeKey($createdAccessor->getID(), $tagKey->getID());

		$remainingTagKey = $this->clp->accessors()->getAssignedKeyByTagID($createdAccessor->getID(), $tag->getID());
		$remainingTagKeyByNumber = $this->clp->accessors()->getAssignedKeyByTagNumber($createdAccessor->getID(), $tag->getTagNumber());

		$this->assertNull($remainingTagKey);
		$this->assertNull($remainingTagKeyByNumber);

		$this->clp->accessors()->deleteAccessor($createdAccessor->getID());

	}

}