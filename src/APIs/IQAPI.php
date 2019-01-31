<?php
/**
 * clp-php-sdk
 * IQAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:15
 */

namespace Clay\CLP\APIs;


use Clay\CLP\Structs\IQ;
use Clay\CLP\Structs\IQHardware;
use Clay\CLP\Structs\IQHardwareTree;
use Clay\CLP\Structs\NewIQRegistration;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\MultiPageResponse;
use Illuminate\Support\Collection;

class IQAPI extends AbstractAPI {

	/**
	 * Gets a list of IQs
	 * @param array $filters
	 * @return MultiPageResponse
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 * @throws \Exception
	 */
	public function getIQs(array $filters = []) : MultiPageResponse {
		$response = $this->client->get('iqs' . $this->buildODataFiltersParameter($filters));
		return new MultiPageResponse($response->content, $this->client, IQ::class);
	}

	/**
	 * Gets a single IQ by its' ID
	 * @param string $iqID
	 * @return IQ
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getIQ(string $iqID) : IQ {
		$response = $this->client->get('iqs/' . $iqID);
		return new IQ((array) $response->content);
	}

	/**
	 * @param NewIQRegistration $registration
	 * @return IQ
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function registerIQ(NewIQRegistration $registration) : IQ {
		$response = $this->client->post('iqs', (array) $registration);
		return new IQ((array) $response->content);
	}

	/**
	 * @param string $iqID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function deleteIQ(string $iqID) {
		return $this->client->delete('iqs/' . $iqID, []);
	}

	/**
	 * @param string $iqID
	 * @return Collection|IQHardware[]
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function getHardwareTree(string $iqID) : Collection {
		$response = $this->client->get('iqs/' . $iqID . '/tree');
		return collect($response->content)
			->map(function ($item) {
				return new IQHardware((array) $item);
			});
	}

	/**
	 * @param IQHardwareTree $tree
	 * @param string $accessorID
	 * @return array|object
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function setHardwareTree(IQHardwareTree $tree, string $accessorID) {

		$newIQTree = $tree
			->getAllHardware()
			->map(function ($hw) use ($tree) { /* @var $hw \Clay\CLP\Structs\IQHardware */
				return [
					"id" => $tree->getIQID(),
				    "hardware_type" => $hw->getType(),
				    "mac" => $hw->getMacAddress(),
				    "customer_reference" => $hw->getCustomerReference(),
				];
			})
			->toArray();

		return $this->client->put('iqs/' . $tree->getIQID() . '/tree', [
			'accessor_id' => $accessorID,
			'iq_tree_items' => $newIQTree,
		]);
	}

}