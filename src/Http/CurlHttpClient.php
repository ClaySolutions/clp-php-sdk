<?php
namespace Clay\CLP\Http;

use Clay\CLP\Contracts\AuthorizationProvider;
use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Ixudra\Curl\Builder;
use Ixudra\Curl\CurlService;

final class CurlHttpClient extends AbstractHttpClient {

	/**
	 * The CURL service
	 * @var CurlService
	 */
	protected $curl;

	public function __construct(string $baseURL, array $defaultHeaders = [], ?AuthorizationProvider $authProvider = null) {
		parent::__construct($baseURL, $defaultHeaders, $authProvider);
		$this->curl = new CurlService();
	}

	/**
	 * Builds an HTTP request with CURL
	 * @param string $url
	 * @param array $headers
	 * @param array $payload
	 * @param bool $isJsonPayload
	 * @param bool $skipDefaultHeaders
	 * @return Builder
	 */
	protected function buildRequest(string $url, array $headers = [], array $payload = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false) : Builder {

		$requestHeaders = $this->buildHeaders($headers, $skipDefaultHeaders);

		$request = $this->curl->to($url);

		if($isJsonPayload) {
			$request->asJson()
				->withContentType($isJsonPayload ? 'application/json' : 'x-www-form-urlencoded')
				->asJsonResponse();
		}

		if(count($payload) > 0) {
			$request->withData($payload);
		}

		//if($this->config->get('clp.debug_curl') === true) {
		//	$request->enableDebug($this->config->get('clp.debug_curl_file'));
		//}

		return $request
			->withHeaders($requestHeaders)
			->returnResponseObject();
	}

	/**
	 * Parses the response for the request.
	 *
	 * @param mixed $response The response from the CURL wrapper.
	 * @param string $url
	 * @return HttpResponse The parsed response, decoded from JSON
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	protected function parseResponse($response, string $url): HttpResponse {

		if(!is_array($response->content) && !is_object($response->content)) {
			$response->content = json_decode($response->content, true);
		}

		$this->checkForFailure($response->status, $url, $response);

		return new HttpResponse($response->status, (array) $response->content);
	}

	/**
	 * Runs a POST request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $payload The request payload.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $isJsonPayload
	 * @param bool $skipDefaultHeaders
	 * @return HttpResponse The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function post(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse {

		$url = $this->generateEndpointURL($path);

		$request = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders);
		$response = $request->post();

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
	 * @return HttpResponse The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function put(string $path, array $payload = [], array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse {

		$url = $this->generateEndpointURL($path);

		$request = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders);
		$response = $request->put();

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
	 * @return HttpResponse The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function patch(string $path, array $payload, array $headers = [], bool $isJsonPayload = true, bool $skipDefaultHeaders = false): HttpResponse {

		$url = $this->generateEndpointURL($path);

		$request = $this->buildRequest($url, $headers, $payload, $isJsonPayload, $skipDefaultHeaders);
		$response = $request->patch();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a GET request on the client.
	 *
	 * @param string $path The API path.
	 * @param array $headers Additional headers, if any. Will be merged with default headers.
	 * @param bool $skipDefaultHeaders
	 * @return HttpResponse The response.
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function get(string $path, array $headers = [], bool $skipDefaultHeaders = false): HttpResponse {

		$url = $this->generateEndpointURL($path);

		$request = $this->buildRequest($url, $headers, [], true, $skipDefaultHeaders);
		$response = $request->get();

		return $this->parseResponse($response, $url);

	}

	/**
	 * Runs a DELETE request on the client.
	 *
	 * @param string $path
	 * @param array $headers
	 * @param bool $skipDefaultHeaders
	 * @return HttpResponse
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function delete(string $path, array $headers = [], bool $skipDefaultHeaders = false): HttpResponse {

		$url = $this->generateEndpointURL($path);

		$request = $this->buildRequest($url, $headers, [], true, $skipDefaultHeaders);
		$response = $request->delete();

		return $this->parseResponse($response, $url);

	}


}