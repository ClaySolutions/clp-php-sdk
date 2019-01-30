<?php
/**
 * clp-php-sdk
 * AccessNotAllowed.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:47
 */

namespace Clay\CLP\Exceptions;


class AccessNotAllowed extends \Exception {

	public function __construct(string $url, int $statusCode, string $message = "No message given") {
		parent::__construct("CLP API denied access for {$url} with status {$statusCode}: {$message}", 40100);
	}

}