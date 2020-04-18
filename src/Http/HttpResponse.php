<?php
declare(strict_types=1);
namespace Clay\CLP\Http;

final class HttpResponse {

	/**
	 * @property-read int
	 */
	public $statusCode;

	/**
	 * @property-read array
	 */
	public $content;

	/**
	 * HttpResponse constructor.
	 * @param int $statusCode
	 * @param array $content
	 */
	public function __construct(int $statusCode, array $content) {
		$this->statusCode = $statusCode;
		$this->content = $content;
	}


}