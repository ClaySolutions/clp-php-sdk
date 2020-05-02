<?php
/**
 * clp-php-sdk
 * MultiPageResponse.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:24
 */

namespace Clay\CLP\Utilities;

use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Clay\CLP\Structs\APIRequest;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use stdClass;

class MultiPageResponse {

	/**
	 * The API response.
	 * @var array
	 */
	private $response;

	/**
	 * The class to cast page items to.
	 * @var \stdClass|null
	 */

	private $itemClass = null;

	/**
	 * The client to request the next page with.
	 * @var AbstractHttpClient
	 */
	private $client;

	/**
	 * The cast set of items for the response.
	 * @var array
	 */
	protected $items = null;

	/**
	 * MultiPageResponse constructor.
	 *
	 * @param object|mixed $response The received response from the API
	 * @param AbstractHttpClient $client The client that will be used to request more pages
	 * @param stdClass $itemClass [optiona] The class to cast each item with. If null, will return array results.
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($response, AbstractHttpClient $client, $itemClass = null) {

		if(!isset($response->items)) {
			throw new InvalidArgumentException('Given response is not a MultiPageResponse!');
		}

		$this->response = $response;
		$this->itemClass = $itemClass;
		$this->client = $client;
	}

	/**
	 * Gets the array of items from the currently loaded page.
	 * @return array
	 */
	private function getCurrentPageItems() : array {
		if($this->items === null) {
			$this->items = (array) ($this->response->items ?? []);
		}

		return (array) $this->items;
	}

	/**
	 * Checks if the current page has items.
	 * @return bool
	 */
	public function hasItems() : bool {
		return count($this->response->items ?? []) > 0;
	}

	/**
	 * Checks if the request has a next page available.
	 * @return bool
	 */
	public function hasNextPage() : bool {
		return $this->response->next_page_link !== null;
	}

	/**
	 * Returns a collection of items for the current page.
	 * @return Collection
	 */
	public function items() : Collection {
		return collect($this->getCurrentPageItems())
			->map(function ($item) {

				if($this->itemClass === null) {
					return (array) $item;
				}

				return new $this->itemClass((array) $item);

			});
	}

	/**
	 * Fetches the next page of results.
	 *
	 * @return MultiPageResponse
	 *
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 * @throws AccessNotAllowed
	 * @throws Exception
	 */
	public function fetchNextPage() : ?self {

		if(!$this->hasNextPage()) {
			return null;
		}

		$nextPageResponse = $this->client->get($this->response->next_page_link);

		return new static(
			$nextPageResponse,
			$this->client,
			$this->itemClass
		);
	}

	/**
	 * Fetches all records available in a given endpoint, by following the next page URL link up until the max records
	 * count is reached. If max records is 0, it will continue to fetch until the next_page_link comes up empty.
	 *
	 * @param APIRequest $request
	 * @param AbstractHttpClient $client
	 * @param int $maxRecords
	 * @param string|null $entityClass
	 *
	 * @return Collection
	 *
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public static function fetchFullCollection(APIRequest $request, AbstractHttpClient $client, int $maxRecords = 0, ?string $entityClass = null): Collection {

		$fetched = 0;
		$results = collect([]);
		$batchRequestPath = $request->getEndpoint();

		if($maxRecords === 0) {
			$maxRecords = PHP_INT_MAX;
		}

		while($fetched < $maxRecords) {

			$batch = $client->get($batchRequestPath, $request->getHeaders(), $request->shouldSkipDefaultHeaders());

			if(!$batch || !$batch->content || !$batch->content->items) {
				break;
			}

			$fetched += count((array) $batch->content->items);
			$results = $results->concat((array) $batch->content->items);

			if(!isset($batch->content->next_page_link) || !$batch->content->next_page_link) {
				break;
			}

			$batchRequestPath = $batch->content->next_page_link;

		}

		return (new Collection($results->take($maxRecords)))
			->map(static function ($item) use ($entityClass) {

				if($entityClass === null) {
					return (array) $item;
				}

				return new $entityClass((array) $item);

			});

	}

}