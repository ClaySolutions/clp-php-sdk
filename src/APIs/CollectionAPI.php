<?php
/**
 * clp-php-sdk
 * CollectionAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 11:32
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Structs\Accessor;
use Clay\CLP\Structs\Collection;
use Clay\CLP\Structs\NewCollection;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;

class CollectionAPI extends AbstractAPI {

	public function getCollection(string $collectionID) : ?Collection {
		$response = $this->client->get('collections' . $this->buildODataFiltersParameter(["id eq '{$collectionID}'"]));
		$list = new MultiPageResponse($response->content, $this->client, Collection::class);
		return $list->items()->first();
	}

	public function getCollections(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('collections' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Collection::class);
	}

	public function createCollection(NewCollection $newCollection) : Collection {
		$response = $this->client->post('collections', (array) $newCollection);
		return new Collection($response->content);
	}

	public function updateCollection(string $collectionID, NewCollection $updatedCollection) : Collection {
		$response = $this->client->patch('collections/' . $collectionID, (array) $updatedCollection);
		return new Collection($response->content);
	}

	public function deleteCollection(string $collectionID) {
		return $this->client->delete('collections/' . $collectionID);
	}

	public function getAccessorSettings(string $collectionID) : MultiPageResponse {
		$response = $this->client->get('collections/' . $collectionID . '/accessor_settings');
		return new MultiPageResponse($response->content, $this->client, Accessor::class);
	}

}