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


abstract class AbstractAPI {

	protected $client;
	protected static $instance;

	public function __construct(AbstractHttpClient $client) {
		$this->client = $client;
	}

	public function buildODataFiltersParameter(array $filters = []) : string {

		if(sizeof($filters) <= 0) return '';

		$filteringString = urlencode(
			collect($filters)
				->map(function ($filter) {
					return ("({$filter})");
				})
				->implode(' and ')
		);

		return '?$filter=' . $filteringString;

	}

	protected static $instances = [];

	public static function getInstance(AbstractHttpClient $client) : self {
		$className = get_called_class();

		if(!isset(self::$instances[$className])) {
			self::$instances[$className] = new static($client);
		}

		return self::$instances[$className];
	}

}