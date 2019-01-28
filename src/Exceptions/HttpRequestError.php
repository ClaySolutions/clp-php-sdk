<?php
/**
 * clp-php-sdk
 * HttpRequestError
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:52
 */

namespace Clay\CLP\Exceptions;


class HttpRequestError extends \Exception {

	public function __construct(string $url, int $statusCode, string $message = "No message given") {
		parent::__construct("CLP API returned error status {$statusCode} for {$url}: {$message}", 11001);
	}

}