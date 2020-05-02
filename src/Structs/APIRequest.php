<?php
declare(strict_types=1);
namespace Clay\CLP\Structs;

final class APIRequest {

	private $endpoint;
	private $headers;
	private $skipDefaultHeaders;

	/**
	 * @param string $endpoint The target API endpoint
	 * @param array $headers The additional headers to send
	 * @param bool $skipDefaultHeaders Should the request skip the default headers configured for this API?
	 */
	public function __construct(string $endpoint, array $headers = [], bool $skipDefaultHeaders = false) {
		$this->endpoint = $endpoint;
		$this->headers = $headers;
		$this->skipDefaultHeaders = $skipDefaultHeaders;
	}

	public function getEndpoint() : string {
		return $this->endpoint;
	}

	public function getHeaders(): array {
		return $this->headers;
	}

	public function shouldSkipDefaultHeaders(): bool {
		return $this->skipDefaultHeaders;
	}
}