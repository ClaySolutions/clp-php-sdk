<?php
/**
 * clp-php-sdk
 * RepeaterAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:37
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Structs\Repeater;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;

class RepeaterAPI extends AbstractAPI {

	/**
	 * @param string $repeaterID
	 * @return Repeater
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getRepeater(string $repeaterID) : Repeater {
		$response = $this->client->get('repeaters/' . $repeaterID);
		return new Repeater($response->content);
	}

	/**
	 * @param array $filters
	 * @return MultiPageResponse
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getRepeaters(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('repeaters' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, Repeater::class);
	}

	/**
	 * @param string $repeaterID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function deleteRepeater(string $repeaterID) {
		return $this->client->delete('repeaters/' . $repeaterID);
	}

}