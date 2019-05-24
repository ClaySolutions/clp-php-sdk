<?php
/**
 * clp-php-sdk
 * EntriesAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-05-01, 15:05
 */

namespace Clay\CLP\APIs;


use Carbon\Carbon;
use Clay\CLP\Structs\Entry;
use Clay\CLP\Utilities\AbstractAPI;
use Illuminate\Support\Collection;

class EntriesAPI extends AbstractAPI {

	/**
	 * @param array $filters
	 * @param int $max
	 * @return Collection
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function fetchEntries($filters = [], int $max = 100) : Collection {

		$results = $this->client->get('entries' .
			$this->buildODataFiltersParameter($filters) .
			"&\$inlinecount=allpages" .
			"&\$orderby=local_date_time%20desc" .
			"&\$top={$max}");

		return collect($results->content->items)
			->map(function ($item) {
				return new Entry((array) $item);
			});

	}

	/**
	 * @param array $filters
	 * @param int $max
	 * @return Collection
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 * @throws \Exception
	 */
	public function fetchAllEntries($filters = [], int $max = 1000) : Collection {

		$fetched = 0;
		$results = collect([]);
		$continuationTokenQuery = "";

		while($fetched < $max) {

			$batch = $this->client->get('entries' .
				$this->buildODataFiltersParameter($filters) .
				"&\$inlinecount=allpages" .
				"&\$orderby=local_date_time%20desc" .
				"&\$top={$max}" .
				$continuationTokenQuery);

			$fetched += sizeof($batch->content->items);
			$results = $results->concat($batch->content->items);

			if(!isset($batch->content->continuation_token)) {
				break;
			}

			$continuationTokenQuery = "&\$continuationToken={$batch->content->continuation_token}";
		}

		return collect($results->take($max))
			->map(function ($item) {
				return new Entry((array) $item);
			});
	}

	/**
	 * @param Carbon $timestamp
	 * @param int $max
	 * @param array $filters
	 * @return Collection
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function fetchEntriesAfterTimestamp(Carbon $timestamp, int $max = 100, array $filters = []) : Collection {

		$formattedDate = $timestamp->toIso8601ZuluString();

		array_push($filters, "utc_date_time ge DateTime'{$formattedDate}'");

		return $this->fetchAllEntries($filters, $max);
	}

}