<?php
/**
 * clp-php-sdk
 * VaultClient.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 15:56
 */

namespace Clay\CLP\Clients;


use Carbon\Carbon;
use Clay\CLP\Structs\AccessToken;
use Clay\CLP\Http\AbstractHttpClient;

class VaultClient extends AbstractHttpClient {

	/**
	 * @var AccessToken|null $accessToken
	 */
	private $accessToken = null;

	/**
	 * @return string
	 */
	public function getEndpointBaseURL(): string {
		return str_finisH($this->config->get('vault.host'), '/');
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function getDefaultHeaders(): array {
		return [
			"Accept: application/json",
			"X-Vault-Token: {$this->provideVaultToken()->getAccessToken()}",
		];
	}

	/**
	 * Generates an access token to use in the vault.
	 * Will always generate a new token.
	 * If you just need a token to make calls, @see self::provideVaultToken()
	 *
	 * @return AccessToken
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function generateVaultToken() {

		$response = $this->post(
			'v1/auth/approle/login',
			[
				'role_id' => $this->config->get('vault.role_id'),
				'secret_id' => $this->config->get('vault.secret_id'),
			],
			[
				'Accept: application/json'
			],
			true,
			true
		);

		if(!isset($response->content) || !isset($response->content->auth) || !isset($response->content->auth->client_token)) {
			throw new \Exception("Invalid token response from vault server!");
		}

		$generatedAt = Carbon::now();

		return $this->accessToken = new AccessToken(
			[
				'access_token' => $response->content->auth->client_token,
				'token_type' => '',
				'expires_in' => intval($response->content->auth->lease_duration)
			],
			$generatedAt
		);

	}

	/**
	 * Provides an access token to use with the vault.
	 * If a previous token was generated and is still valid, will return that.
	 *
	 * @return AccessToken|null
	 * @throws \Exception
	 */
	public function provideVaultToken() {
		if($this->accessToken === null || $this->accessToken->hasExpired()) {
			return $this->generateVaultToken();
		}

		return $this->accessToken;
	}

	/**
	 * Signs a variable in the vault and generates a signature.
	 *
	 * @param string $variable
	 * @param string $input
	 * @return string|null
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function sign(string $variable, string $input, bool $isBase64Encoded = false) : ?string {

		$vaultMount = $this->config->get('vault.mount');

		$response = $this->post("v1/{$vaultMount}/sign/{$variable}/sha2-256", [
			'input' => $isBase64Encoded ? $input : base64_encode($input),
		]);

		return $response->content->data->signature ?? null;


	}

	/**
	 * Verifies if a payload was signed with the given signature.
	 *
	 * @param string $variable
	 * @param string $input
	 * @param string $signature
	 * @return bool
	 * @throws \Clay\CLP\Exceptions\AccessNotAllowed
	 * @throws \Clay\CLP\Exceptions\EmptyResponseFromServer
	 * @throws \Clay\CLP\Exceptions\EndpointNotFound
	 * @throws \Clay\CLP\Exceptions\HttpRequestError
	 */
	public function verify(string $variable, string $input, string $signature, bool $isBase64Encoded = false) : bool {

		$vaultMount = $this->config->get('vault.mount');

		$response = $this->post("v1/{$vaultMount}/verify/{$variable}/sha2-256", [
			'input' => $isBase64Encoded ? $input : base64_encode($input),
			'signature' => $signature,
			'format' => 'base64'
		]);

		if(!isset($response->content->data->valid)) {
			return false;
		}

		return boolval($response->content->data->valid);
	}


}