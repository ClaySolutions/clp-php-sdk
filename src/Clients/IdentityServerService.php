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
use Clay\CLP\Contracts\AuthorizationProvider;
use Clay\CLP\Contracts\HttpClient;
use Clay\CLP\Exceptions\AccessNotAllowed;
use Clay\CLP\Exceptions\EmptyResponseFromServer;
use Clay\CLP\Exceptions\EndpointNotFound;
use Clay\CLP\Exceptions\HttpRequestError;
use Clay\CLP\Structs\AccessToken;
use Clay\CLP\Structs\OAuthParameters;

final class IdentityServerService implements AuthorizationProvider {

	/**
	 * @var HttpClient
	 */
	private $client;

	/**
	 * @var OAuthParameters
	 */
	private $parameters;

	/**
	 * A token that has been previously generated
	 * @var AccessToken|null
	 */
	protected $existingToken = null;

	/**
	 * IdentityServerService constructor.
	 * @param OAuthParameters $parameters
	 * @param HttpClient $client
	 */
	public function __construct(OAuthParameters $parameters, HttpClient $client) {
		$this->parameters = $parameters;
		$this->client = $client;
	}

	/**
	 * Fetches an access token from the Identity Server.
	 * @return AccessToken
	 * @see self::provideAccessToken() instead, if you just need a token for a request.
	 *
	 */
	public function fetchAccessToken() : AccessToken {

		$authResponse = $this->client->post(
			'connect/token',
			[
				'grant_type' => 'client_credentials',
				'scope' => $this->parameters->scope,
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
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
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
		return base64_encode("{$this->parameters->clientId}:{$this->parameters->clientSecret}");
	}

	/**
	 * @return string
	 * @throws AccessNotAllowed
	 * @throws EmptyResponseFromServer
	 * @throws EndpointNotFound
	 * @throws HttpRequestError
	 */
	public function generateAuthorizationHeader(): string {
		$token = $this->provideAccessToken();
		return $token->generateAuthorizationHeader();
	}
}