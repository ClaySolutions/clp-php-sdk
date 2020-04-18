<?php
/**
 * clp-php-sdk
 * TagAPITest.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:48
 */

namespace Tests\Integration;


use Tests\CLPTestCase;

class TagAPITest extends CLPTestCase {

	public function test_can_get_list_of_tags() {

		$tags = $this->clp->tags()->getTags();

		$this->assertInstanceOf('Clay\CLP\Utilities\MultiPageResponse', $tags);
		$this->assertGreaterThan(0, $tags->items()->count());
		$this->assertInstanceOf('Clay\CLP\Structs\Tag', $tags->items()->first());

	}

	public function test_can_find_tag_by_number() {

		$tagNumber = $this->config->get('clp.test.tag_number');
		$foundTag = $this->clp->tags()->getTagByNumber($tagNumber);

		$this->assertInstanceOf('Clay\CLP\Structs\Tag', $foundTag);

	}

	public function test_can_get_single_tag() {

		$tagNumber = $this->config->get('clp.test.tag_number');
		$existingTag = $this->clp->tags()->getTags(["tag_number eq '{$tagNumber}'"])->items()->first();

		$tag = $this->clp->tags()->getTag($existingTag->getID());

		$this->assertInstanceOf('Clay\CLP\Structs\Tag', $tag);
		$this->assertEquals($existingTag->getID(), $tag->getID());

	}

}