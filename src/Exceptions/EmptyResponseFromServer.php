<?php
/**
 * clp-php-sdk
 * EmptyResponseFromServer.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:55
 */

namespace Clay\CLP\Exceptions;


class EmptyResponseFromServer extends \Exception {

	public function __construct(string $url, $response = null) {
		parent::__construct("CLP API returned an empty response for: {$url} " . (($response !== null) ? json_encode($response) : ''), 20400);
	}

}