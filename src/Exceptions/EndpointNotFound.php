<?php
/**
 * clp-php-sdk
 * EndpointNotFoundException.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:46
 */

namespace Clay\CLP\Exceptions;


class EndpointNotFound extends \Exception {

	public function __construct(string $url) {
		parent::__construct("CLP API returned status 404 Not Found: {$url}", 40400);
	}

}