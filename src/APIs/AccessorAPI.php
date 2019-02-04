<?php
/**
 * clp-php-sdk
 * AccessorAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 10:07
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Structs\Accessor;
use Clay\CLP\Structs\Key;
use Clay\CLP\Structs\NewAccessor;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;

class AccessorAPI extends AbstractAPI {

	public function getAccessor(string $accessorID) : Accessor {
		$response = $this->client->get('accessors/' . $accessorID);
		return new Accessor((array) $response->content);
	}

	public function getAccessors(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('accessors' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Accessor::class);
	}

	public function createAccessor(NewAccessor $accessor) : Accessor {
		$response = $this->client->post('accessors', (array) $accessor);
		return new Accessor((array) $response->content);
	}

	public function deleteAccessor(string $accessorID) {
		return $this->client->delete('accessors/' . $accessorID);
	}

	public function updateAccessor(string $accessorID, NewAccessor $accessor) : Accessor {
		$response = $this->client->patch('accessors/' . $accessorID, (array) $accessor);
		return new Accessor((array) $response->content);
	}

	public function getAssignedKeys(string $accessorID) : MultiPageResponse {
		$response = $this->client->get('accessors/' . $accessorID .  '/keys');
		return new MultiPageResponse($response->content, $this->client, Key::class);
	}

	public function assignTag(string $accessorID, string $tagID, bool $isBlocked = false) : Key {
		$response = $this->client->post('accessors/' . $accessorID . '/keys', [
			'tag_id' => $tagID,
			'blocked' => $isBlocked,
		]);

		return new Key((array) $response->content);
	}

	public function getAssignedKeyByTagID(string $accessorID, string $tagID) : ?Key {
		return $this->getAssignedKeys($accessorID)
			->items()
			->first(function ($key) use ($tagID) { /* @var $key \Clay\CLP\Structs\Key */
				return $key->getKeyID() === $tagID;
			});
	}

	public function getAssignedKeyByTagNumber(string $accessorID, string $tagNumber) : ?Key {
		return $this->getAssignedKeys($accessorID)
			->items()
			->first(function ($key) use ($tagNumber) { /* @var $key \Clay\CLP\Structs\Key */
				return $key->getKeyNumber() === $tagNumber;
			});
	}

	public function unassignTag(string $accessorID, string $tagID) {
		$foundKey = $this->getAssignedKeyByTagID($accessorID, $tagID);

		if(!$foundKey) {
			throw new \InvalidArgumentException("The given tag ID ({$tagID}) does not belong to this accessor ({$accessorID})");
		}

		$this->removeKey($accessorID, $foundKey->getID());
	}

	public function removeKey(string $accessorID, string $keyID, bool $forceDelete = false) {
		return $this->client->delete('accessors/' . $accessorID . '/keys/' . $keyID . ($forceDelete ? '?force=true' : ''));
	}

}