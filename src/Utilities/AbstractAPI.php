<?php
/**
 * clp-php-sdk
 * AbstractAPI.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:16
 */

namespace Clay\CLP\Utilities;


use Clay\CLP\Contracts\HttpClient;

abstract class AbstractAPI {

	protected $client;
	protected static $instance;

	public function __construct(HttpClient $client) {
		$this->client = $client;
	}

	public function buildODataFiltersParameter(array $filters = []) : string {

		if(count($filters) <= 0) {
			return '?';
		}

		$filteringString = urlencode(
			collect($filters)
				->map(static function ($filter) {
					return ("({$filter})");
				})
				->implode(' and ')
		);

		return '?$filter=' . $filteringString;

	}

	protected static $instances = [];

	public static function getInstance(HttpClient $client) : self {
		$className = static::class;

		if(!isset(self::$instances[$className])) {
			self::$instances[$className] = new static($client);
		}

		return self::$instances[$className];
	}

}