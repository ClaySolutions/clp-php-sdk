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

use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFoundException;
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
	 * @var Repository
	 */
	protected $config;

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
		return str_finish($this->getEndpointBaseURL(), '/') . $path;
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
	 * Builds an HTTP request with CURL
	 * @param string $url
	 * @param array $headers
	 * @param bool $isJsonPayload
	 * @return Builder
	 */
	protected function buildRequest(string $url, array $headers = [], array $payload = [], bool $isJsonPayload = true) : Builder {

		$requestHeaders = array_merge($this->getDefaultHeaders(), $headers);

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
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	protected function parseResponse($response, string $url) {

		if(!is_array($response->content) && !is_object($response->content)) {
			$response->content = json_decode($response->content);
		}

		if(in_array($response->status ?? 0, self::HTTP_SUCCESS_CODES)) {
			return $response;
		}

		switch($response->status) {
			case 404: throw new EndpointNotFoundException($url);
			case 400:
			case 500:
				throw new HttpRequestError($url, $response->status, json_encode($response));
		}

		throw new EmptyResponseFromServer($url);
	}

	/**
	 * Runs a POST request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $payload The request payload.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $isJsonPayload
	 * @return array|object The response.
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	public function post(string $path, array $payload, array $headers = [], bool $isJsonPayload = true) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload)
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
	 * @return array|object The response.
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	public function put(string $path, array $payload, array $headers = [], bool $isJsonPayload = true) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload)
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
	 * @return array|object The response.
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	public function patch(string $path, array $payload, array $headers = [], bool $isJsonPayload = true) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers, $payload, $isJsonPayload)
			->patch();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a GET request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @return array|object The response.
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	public function get(string $path, array $headers = []) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers)
			->get();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a DELETE request on the client.
	 *
	 * @param string $path
	 * @param array $headers
	 * @return array|object
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFoundException
	 * @throws HttpRequestError
	 */
	public function delete(string $path, array $headers = []) {

		$url = $this->generateEndpointURL($path);

		$response = $this->buildRequest($url, $headers)
			->delete();

		return $this->parseResponse($response, $url);

	}
}