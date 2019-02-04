<?php
/**
 * clp-php-sdk
 * TagAPIphp
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:45
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Structs\Tag;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;

class TagAPI extends AbstractAPI {

	public function getTag(string $tagID) : Tag {
		$response = $this->client->get('tags/' . $tagID);
		return new Tag((array) $response->content);
	}

	public function getTags(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('tags' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Tag::class);
	}

	public function getTagByNumber(string $tagNumber) : ?Tag {
		$response = $this->client->get('tags' . $this->buildODataFiltersParameter(["tag_number eq '{$tagNumber}'"]));
		$list = new MultiPageResponse($response->content, $this->client, Tag::class);

		return $list->items()->first() ?? null;
	}

	public function deleteTag(string $tagID) {
		return $this->client->delete('tags/' . $tagID);
	}

}