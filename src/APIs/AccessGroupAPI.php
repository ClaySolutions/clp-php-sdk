<?php
/**
 * clp-php-sdk
 * AccessGroupAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 13:33
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Clay\CLP\Structs\AccessGroup;
use Clay\CLP\Structs\Accessor;
use Clay\CLP\Structs\APIRequest;
use Clay\CLP\Structs\Lock;
use Clay\CLP\Structs\NewAccessGroup;
use Clay\CLP\Structs\TimeSchedule;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;
use Illuminate\Support\Collection;

class AccessGroupAPI extends AbstractAPI {

	public function getGroup(string $groupID) : AccessGroup {
		$response = $this->client->get('access_groups/' . $groupID);
		return new AccessGroup((array) $response->content);
	}

	public function getGroups(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('access_groups' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, AccessGroup::class);
	}

	public function createGroup(NewAccessGroup $newGroup) : AccessGroup {
		$response = $this->client->post('access_groups', (array) $newGroup);
		return new AccessGroup((array) $response->content);
	}

	public function updateGroup(string $groupID, NewAccessGroup $newGroup) : AccessGroup {
		$response = $this->client->patch('access_groups/' . $groupID, (array) $newGroup);
		return new AccessGroup((array) $response->content);
	}

	public function deleteGroup(string $groupID) {
		return $this->client->delete('access_groups/' . $groupID);
	}

	public function getGroupLocks(string $groupID, array $filters = []) : MultiPageResponse {
		$response = $this->client->get('access_groups/' . $groupID . '/locks' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Lock::class);
	}

	public function updateGroupLocks(string $groupID, array $idsToAdd = [], array $idsToRemove = []) {
		return $this->client->patch('access_groups/' . $groupID . '/locks', [
			'add_ids' => $idsToAdd,
			'remove_ids' => $idsToRemove,
		]);
	}

	public function getGroupAccessors(string $groupID, array $filters = []) : MultiPageResponse {
		$response = $this->client->get('access_groups/' . $groupID . '/accessors' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Accessor::class);
	}

	/**
	 * @param string $groupID
	 * @param array $idsToAdd
	 * @param array $idsToRemove
	 *
	 * @return array|object
	 *
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function updateGroupAccessors(string $groupID, array $idsToAdd = [], array $idsToRemove = []) {
		return $this->client->patch('access_groups/' . $groupID . '/accessors', [
			'add_ids' => $idsToAdd,
			'remove_ids' => $idsToRemove,
		]);
	}

	public function getTimeSchedules(string $groupID, array $filters = []) : MultiPageResponse {
		$response = $this->client->get('access_groups/' . $groupID . '/time_schedules' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, TimeSchedule::class);
	}

	public function addTimeSchedule(string $groupID, TimeSchedule $timeSchedule) : TimeSchedule {
		$response = $this->client->post('access_groups/' . $groupID . '/time_schedules', $timeSchedule->toArray(true));
		return new TimeSchedule((array) $response->content);
	}

	public function removeTimeSchedule(string $groupID, string $scheduleID) {
		return $this->client->delete('access_groups/' . $groupID . '/time_schedules/' . $scheduleID);
	}

	public function updateTimeSchedule(string $groupID, string $scheduleID, TimeSchedule $timeSchedule) : TimeSchedule {
		$response = $this->client->patch('access_groups/' . $groupID . '/time_schedules/' . $scheduleID, $timeSchedule->toArray(true));
		return new TimeSchedule((array) $response->content);
	}

	/**
	 * @param string $groupID
	 * @param array $filters
	 * @param int $maxRecords
	 *
	 * @return Lock[]|Collection
	 *
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function fetchAllGroupLocks(string $groupID, array $filters = [], int $maxRecords = 0): Collection {
		$request = new APIRequest(
			'access_groups/' . $groupID . '/locks' . $this->buildODataFiltersParameter($filters)
		);

		return MultiPageResponse::fetchFullCollection($request, $this->client, $maxRecords, Lock::class);
	}

	/**
	 * @param string $groupID
	 * @param array $filters
	 * @param int $maxRecords
	 *
	 * @return Accessor[]|Collection
	 *
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function fetchAllGroupAccessors(string $groupID, array $filters = [], int $maxRecords = 0) : Collection {
		$request = new APIRequest(
			'access_groups/' . $groupID . '/accessors' . $this->buildODataFiltersParameter($filters)
		);

		return MultiPageResponse::fetchFullCollection($request, $this->client, $maxRecords, Accessor::class);
	}

}