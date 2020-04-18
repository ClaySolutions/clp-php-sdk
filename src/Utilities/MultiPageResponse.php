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


use Clay\CLP\Contracts\HttpClient;
use Illuminate\Support\Collection;
use RuntimeException;

class MultiPageResponse {

	/**
	 * The API response.
	 * @var array
	 */
	private $response;

	/**
	 * The class to cast page items to.
	 * @var string|null
	 */

	private $itemClass = null;

	/**
	 * The client to request the next page with.
	 * @var HttpClient
	 */
	private $client;

	/**
	 * The cast set of items for the response.
	 * @var array
	 */
	protected $items = null;

	/**
	 * MultiPageResponse constructor.
	 * @param object|mixed $response The received response from the API
	 * @param HttpClient $client The client that will be used to request more pages
	 * @param $itemClass [optional] The class to cast each item with. If null, will return array results.
	 * @throws RuntimeException
	 */
	public function __construct($response, HttpClient $client, ?string $itemClass = null) {

		if(!isset($response['items'])) {
			throw new RuntimeException('Given response is not a MultiPageResponse!');
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
			$this->items = (array) ($this->response['items'] ?? []);
		}

		return $this->items;
	}

	/**
	 * Checks if the current page has items.
	 * @return bool
	 */
	public function hasItems(): bool {
		return count($this->response['items'] ?? []) > 0;
	}

	/**
	 * Checks if the request has a next page available.
	 * @return bool
	 */
	public function hasNextPage(): bool {
		return $this->response['next_page_link'] !== null;
	}

	/**
	 * Returns a collection of items for the current page.
	 * @return Collection
	 */
	public function items(): Collection {
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
	 */
	public function fetchNextPage(): ?self {

		if(!$this->hasNextPage()) {
			return null;
		}

		$nextPageResponse = $this->client->get($this->response['next_page_link']);

		return new static(
			$nextPageResponse,
			$this->client,
			$this->itemClass
		);
	}

}