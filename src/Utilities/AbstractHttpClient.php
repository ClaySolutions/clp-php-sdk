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

namespace Clay\CLP\Utilities;

use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Illuminate\Contracts\Config\Repository;
use Ixudra\Curl\Builder;
use Ixudra\Curl\CurlService;


abstract class AbstractHttpClient {

	/**
	 * What HTTP status codes are considered succesful?
	 */
	const HTTP_SUCCESS_CODES = [200, 201, 202, 204];

	/**
	 * The CURL service
	 * @var \Ixudra\Curl\CurlService
	 */
	protected $curl;

	/**
	 * The configuration repository.
	 * @var Repository
	 */
	protected $config;

	/**
	 * The auth header provider closure.
	 * @var \Closure|null
	 */
	protected $authorizationHeaderProvider = null;

	public abstract function getEndpointBaseURL() : string;

	/**
	 * AbstractHttpClient constructor.
	 * @param Repository $config
	 */
	public function __construct(Repository $config) {
		$this->curl = new CurlService();
		$this->config = $config;
	}

	/**
	 * Generates a final endpoint URL based on a path.
	 * @param string $path
	 * @return string
	 */
	public function generateEndpointURL(string $path) : string {
		if($this->isFullURL($path)) return str_finish($path, '/');
		return str_finish($this->getEndpointBaseURL(), '/') . $path;
	}

	/**
	 * Checks if a given path/URI is a full URL or local path.
	 * @param string $input
	 * @return bool True if full URL, false if local path
	 */
	protected function isFullURL(string $input) : bool {
		return (substr($input, 0, 7) === 'http://')
			|| (substr($input, 0, 8) === 'https://');
	}

	/**
	 * The default headers for requests on this client.
	 * @return array
	 */
	protected function getDefaultHeaders() : array {
		return [
			'Accept: application/json',
		];
	}

	/**
	 * Sets a callback to generate an authorization header for each request.
	 * The authorization header is expected to generate the string following 'Authorization:' on the header.
	 * @param \Closure $closure
	 */
	public function setAuthorizationHeaderProvider(\Closure $closure) {
		$this->authorizationHeaderProvider = $closure;
	}

	/**
	 * Builds an HTTP request with CURL
	 * @param string $url
	 * @param array $headers
	 * @param array $payload
	 * @param bool $isJsonPayload
	 * @return Builder
	 */
	protected function buildRequest(string $url, array $headers = [], array $payload = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false) : Builder {

		$requestHeaders = $skipDefaultHeaders
			? $headers
			: array_merge($this->getDefaultHeaders(), $headers);

		if($this->authorizationHeaderProvider !== null) {
			array_push($requestHeaders, "Authorization: " . ($this->authorizationHeaderProvider)());
		}

		$request = $this->curl->to($url);

		if($isJsonPayload) {
			$request->asJson()
				->withContentType($isJsonPayload ? 'application/json' : 'x-www-form-urlencoded')
				->asJsonResponse();
		}

		if(sizeof($payload) > 0) {
			$request->withData($payload);
		}

		if($this->config->get('clp.debug_mode') === 'on') {
			$request->enableDebug(__DIR__ . '../../../debug.log');
		}

		return $request
			->withHeaders($requestHeaders)
			->returnResponseObject();
	}

	/**
	 * Parses the response for the request.
	 *
	 * @param mixed $response The response from the CURL wrapper.
	 * @param string $url
	 * @return array|object The parsed response, decoded from JSON
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 * @throws AccessNotAllowed
	 */
	protected function parseResponse($response, string $url) {

		if(!is_array($response->content) && !is_object($response->content)) {
			$response->content = json_decode($response->content);
		}

		if(in_array($response->status ?? 0, self::HTTP_SUCCESS_CODES)) {
			return $response;
		}

		switch($response->status) {
			case 404:
				throw new EndpointNotFound($url, json_encode($response));
			case 401:
			case 403:
				throw new AccessNotAllowed($url, $response->status, json_encode($response));
			case 400:
			case 422:
			case 500:
				throw new HttpRequestError($url, $response->status, json_encode($response));
		}

		throw new EmptyResponseFromServer($url, $response);
	}

	/**
	 * Runs a POST request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $payload The request payload.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $isJsonPayload
	 * @param bool $skipDefaultHeaders
	 * @return array|object The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function post(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders)
			->post();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a PUT request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $payload The request payload.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $isJsonPayload
	 * @param bool $skipDefaultHeaders
	 * @return array|object The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function put(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders)
			->put();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a PATCH request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $payload The request payload.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $isJsonPayload
	 * @param bool $skipDefaultHeaders
	 * @return array|object The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function patch(string $path, array $payload, array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders)
			->patch();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a GET request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $skipDefaultHeaders
	 * @return array|object The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function get(string $path, array $headers = [], bool $skipDefaultHeaders = false) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, [], true, $skipDefaultHeaders)
			->get();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a DELETE request on the client.
	 *
	 * @param string $path
	 * @param array $headers
	 * @param bool $skipDefaultHeaders
	 * @return array|object
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function delete(string $path, array $headers = [], bool $skipDefaultHeaders = false) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, [], true, $skipDefaultHeaders)
			->delete();

		return $this->parseResponse($response, $url);

	}
}