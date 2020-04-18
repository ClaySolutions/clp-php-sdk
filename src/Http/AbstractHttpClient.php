<?php
/**
 * clp-php-sdk
 * AbstractHttpClient.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 15:55
 */

namespace Clay\CLP\Http;

use Clay\CLP\Contracts\AuthorizationProvider;
use Clay\CLP\Contracts\HttpClient;
use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;


abstract class AbstractHttpClient implements HttpClient {

	/**
	 * What HTTP status codes are considered successful?
	 */
	protected const HTTP_SUCCESS_CODES = [200, 201, 202, 204];

	/**
	 * @var string
	 */
	protected $baseURL;

	/**
	 * @var array
	 */
	protected $defaultHeaders;

	/**
	 * The configuration repository.
	 * @var Repository
	 */
	protected $config;

	/**
	 * The auth header provider closure.
	 * @var AuthorizationProvider
	 */
	protected $authProvider = null;

	/**
	 * AbstractHttpClient constructor.
	 * @param string $baseURL
	 * @param AuthorizationProvider|null $authProvider
	 */
	public function __construct(string $baseURL, array $defaultHeaders = [], ?AuthorizationProvider $authProvider = null) {
		$this->baseURL = $baseURL;
		$this->defaultHeaders = $defaultHeaders;
		$this->authProvider = $authProvider;
	}

	/**
	 * Generates a final endpoint URL based on a path.
	 * @param string $path
	 * @return string
	 */
	protected function generateEndpointURL(string $path) : string {
        if($this->isFullURL($path)) {
	        return Str::finish($path, '/');
        }

        return Str::finish($this->baseURL, '/') . $path;
	}

	/**
	 * Checks if a given path/URI is a full URL or local path.
	 * @param string $input
	 * @return bool True if full URL, false if local path
	 */
	protected function isFullURL(string $input) : bool {
		return (strpos($input, 'http://') === 0)
			|| (strpos($input, 'https://') === 0);
	}

	/**
	 * @param array $headers
	 * @param bool $skipDefaultHeaders
	 * @return array
	 */
	protected function buildHeaders(array $headers, bool $skipDefaultHeaders = false): array {
		$requestHeaders = $skipDefaultHeaders
			? $headers
			: array_merge($this->defaultHeaders, $headers);

		if($this->authProvider !== null) {
			$requestHeaders[] = 'Authorization: ' . $this->authProvider->generateAuthorizationHeader();
		}

		return $requestHeaders;
	}


	/**
	 * Checks a response for errors, throws exceptions if they're present
	 * @param int $statusCode
	 * @param string $url
	 * @param null $response
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	protected function checkForFailure(int $statusCode, string $url, $response = null): void {

		if(in_array($statusCode ?? 0, self::HTTP_SUCCESS_CODES, true)) {
			return;
		}

		$errorMessageAsJson = json_encode($response, JSON_THROW_ON_ERROR, 512);

		switch($statusCode) {
			case 404:
				throw new EndpointNotFound($url, $errorMessageAsJson);
			case 401:
			case 403:
				throw new AccessNotAllowed($url, $statusCode, $errorMessageAsJson);
			case 400:
			case 422:
			case 500:
				throw new HttpRequestError($url, $statusCode, $errorMessageAsJson);
		}

		throw new EmptyResponseFromServer($url, $errorMessageAsJson);

	}

}