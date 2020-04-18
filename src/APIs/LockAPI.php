<?php
/**
 * clp-php-sdk
 * LockAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-30, 15:13
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Structs\TimeSchedule;
use Clay\CLP\Structs\Key;
use Clay\CLP\Structs\Lock;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;
use http\Exception\InvalidArgumentException;

class LockAPI extends AbstractAPI {

	/**
	 * @param array $filters
	 * @return MultiPageResponse
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 * @throws \Exception
	 */
	public function getLocks(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('locks' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Lock::class);
	}

	/**
	 * @param string $lockID
	 * @return Lock
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getLock(string $lockID) : Lock {
		$response = $this->client->get('locks/' . $lockID);
		return new Lock($response->content);
	}

	/**
	 * @param string $lockID
	 * @return Lock
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function deleteLock(string $lockID) {
		$response = $this->client->delete('locks/' . $lockID);
		return $response->content;
	}

	/**
	 * @param string $lockID
	 * @param string $accessorID
	 * @return Lock
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function detachFromIQ(string $lockID, string $accessorID) : Lock {
		$response = $this->client->patch('locks/' . $lockID . '/detach', [
			'iq_link_state' => 'detached_pending',
			'accessor_id' => $accessorID,
		]);

		return new Lock($response->content);
	}

	/**
	 * @param string $lockID
	 * @param array $filters
	 * @return MultiPageResponse
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 * @throws \Exception
	 */
	public function getOfflineKeys(string $lockID, array $filters = []) : MultiPageResponse {
		$response = $this->client->get('locks/' . $lockID . '/offline_keys' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Key::class);
	}

	/**
	 * @param string $lockID
	 * @param array $keyIDsToAdd
	 * @param array $keyIDsToRemove
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function updateOfflineKeys(string $lockID, array $keyIDsToAdd = [], array $keyIDsToRemove = []) {
		return $this->client->patch('locks/' . $lockID . '/offline_keys', [
			'add_ids' => $keyIDsToAdd,
			'remove_ids' => $keyIDsToRemove,
		]);
	}

	/**
	 * @param string $lockID
	 * @param string $keyID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function removeOfflineKey(string $lockID, string $keyID) {
		return $this->updateOfflineKeys($lockID, [], [$keyID]);
	}

	/**
	 * @param string $lockID
	 * @param string $keyID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function addOfflineKey(string $lockID, string $keyID) {
		return $this->updateOfflineKeys($lockID, [$keyID], []);
	}

	/**
	 * @param string $lockID
	 * @return TimeSchedule
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getEasyOfficeModeSchedule(string $lockID) : ?TimeSchedule {
		try {
			$request = $this->client->get('locks/' . $lockID . '/easy_office_mode_schedule');
		} catch (EndpointNotFound $notFound) {
			return null;
		}

		return new TimeSchedule((array) $request->content);
	}

	/**
	 * @param string $lockID
	 * @param TimeSchedule $schedule
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function setEasyOfficeModeSchedule(string $lockID, TimeSchedule $schedule) {
		return $this->client->put('locks/' . $lockID . '/easy_office_mode_schedule', $schedule->toArray());
	}

	/**
	 * @param string $lockID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function deleteEasyOfficeModeSchedule(string $lockID) {
		return $this->client->delete('locks/' . $lockID . '/easy_office_mode_schedule');
	}

	/**
	 * @param string $lockID
	 * @param int $durationInSeconds
	 * @return array|object
	 * @throws EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function triggerLockRegistrationMode(string $lockID, int $durationInSeconds = 60) {
		if($durationInSeconds < 15) {
			throw new \InvalidArgumentException("Duration in seconds for tag registration should be over 15 seconds.");
		}

		return $this->client->patch('locks/' . $lockID . '/tag_registration', [
			'duration_in_seconds' => $durationInSeconds
		]);
	}

}