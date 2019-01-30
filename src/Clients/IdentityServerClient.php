<?php
/**
 * clp-php-sdk
 * IdentityServerClient.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:07
 */

namespace Clay\CLP\Clients;

use Carbon\Carbon;
use Clay\CLP\Structs\AccessToken;
use Clay\CLP\Utilities\AbstractHttpClient;

class IdentityServerClient extends AbstractHttpClient {

	/**
	 * A token that has been previously generated
	 * @var AccessToken|null
	 */
	protected $existingToken = null;

	public function getEndpointBaseURL(): string {
		return $this->config->get('clp.endpoints.identity_server');
	}

	/**
	 * Fetches an access token from the Identity Server.
	 * @see self::provideAccessToken() instead, if you just need a token for a request.
	 *
	 * @return AccessToken
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function fetchAccessToken() : AccessToken {

		$authResponse = $this->post(
			'connect/token',
			[
				'grant_type' => 'client_credentials',
				'scope' => 'hardware_api',
			],
			[
				"Authorization: Basic {$this->generateClientCredentialsToken()}"
			],
			false
		);

		return $this->existingToken = new AccessToken((array) $authResponse->content, Carbon::now());

	}

	/**
	 * Checks if the existing token is still valid
	 * @return bool
	 */
	protected function isExistingTokenValid() : bool {
		if($this->existingToken === null) {
			return false;
		}

		if(strlen($this->existingToken->getAccessToken()) <= 0) {
			return false;
		}

		return !$this->existingToken->hasExpired();

	}

	/**
	 * Will check if the previously provided token is still valid, and return that.
	 * Else, will generate a new token, and save it for future requests.
	 *
	 * @return AccessToken
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function provideAccessToken() : AccessToken {
		if($this->isExistingTokenValid()) {
			return $this->existingToken;
		}

		return $this->fetchAccessToken();
	}

	/**
	 * Generates the client credentials authorization key for token retrieval.
	 * @return string
	 */
	protected function generateClientCredentialsToken() : string {
		$clientID = $this->config->get('clp.client_id');
		$clientSecret = $this->config->get('clp.client_secret');
		return base64_encode("{$clientID}:{$clientSecret}");
	}

}