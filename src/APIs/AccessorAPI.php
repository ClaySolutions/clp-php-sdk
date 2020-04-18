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
use Clay\CLP\Structs\AccessorDevice;
use Clay\CLP\Structs\Key;
use Clay\CLP\Structs\NewAccessor;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;

class AccessorAPI extends AbstractAPI {

	public function getAccessor(string $accessorID) : Accessor {
		$response = $this->client->get('accessors/' . $accessorID);
		return new Accessor($response->content);
	}

	public function getAccessors(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('accessors' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Accessor::class);
	}

	public function createAccessor(NewAccessor $accessor) : Accessor {
		$response = $this->client->post('accessors', (array) $accessor);
		return new Accessor($response->content);
	}

	public function deleteAccessor(string $accessorID) {
		return $this->client->delete('accessors/' . $accessorID);
	}

	public function updateAccessor(string $accessorID, NewAccessor $accessor) : Accessor {
		$response = $this->client->patch('accessors/' . $accessorID, (array) $accessor);
		return new Accessor($response->content);
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

		return new Key($response->content);
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

	public function getDevices(string $accessorID) : MultiPageResponse {
		$response = $this->client->get('accessors/' . $accessorID . '/devices');
		return new MultiPageResponse($response->content, $this->client, AccessorDevice::class);
	}

	public function getDevice(string $accessorID, string $deviceID) : AccessorDevice {
		$response = $this->client->get('accessors/' . $accessorID . '/devices/' . $deviceID);
		return new AccessorDevice($response->content);
	}

	public function createDevice(string $accessorID, ?string $deviceCustomerReference = null) : AccessorDevice {
		$response = $this->client->post('accessors/' . $accessorID . '/devices', [
			'customer_reference' => $deviceCustomerReference
		]);

		return new AccessorDevice($response->content);
	}

	public function getDeviceMobileKey(string $accessorID, string $deviceID, string $iqID) : ?string {
		$response = $this->client->get('accessors/' . $accessorID . '/devices/' . $deviceID . '/mkey?iq_id=' . $iqID);
		return $response->content->mkey_data ?? null;
	}

	public function deleteDevice(string $accessorID, string $deviceID) {
		return $this->client->delete('accessors/' . $accessorID . '/devices/' . $deviceID);
	}

	public function createDeviceCertificate(string $accessorID, string $deviceID, string $publicKey, string $expiryDate) : string {
		$response = $this->client->post('accessors/' . $accessorID . '/devices/' . $deviceID . '/certificate', [
			'public_key' => $publicKey,
			'expiry_date' => $expiryDate,
		]);

		return $response->content->certificate_data ?? null;
	}

	public function activateDeviceCertificate(string $accessorID, string $deviceID, string $signature) : string {
		if(substr($signature, 0, 9) === 'vault:v1:') {
			$signature = substr($signature, 9);
		}

		$response = $this->client->patch('accessors/' . $accessorID . '/devices/' . $deviceID . '/certificate', [
			'signature' => $signature
		]);

		return $response->content->certificate;
	}

	public function replaceDeviceCertificate(string $accessorID, string $deviceID, string $publicKey, string $expiryDate) : string {
		$response = $this->client->put('accessors/' . $accessorID . '/devices/' . $deviceID . '/certificate', [
			'public_key' => $publicKey,
			'expiry_date' => $expiryDate,
		]);

		return $response->content->certificate_data ?? null;
	}

}