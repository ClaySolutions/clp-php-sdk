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

use Clay\CLP\Structs\AccessToken;
use Clay\CLP\Utilities\AbstractHttpClient;

class IdentityServerClient extends AbstractHttpClient {

	public function getEndpointBaseURL(): string {
		return $this->config->get('clp.endpoints.identity_server');
	}

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

		return new AccessToken((array) $authResponse->content);

	}

	protected function generateClientCredentialsToken() : string {
		$clientID = $this->config->get('clp.client_id');
		$clientSecret = $this->config->get('clp.client_secret');
		return base64_encode("{$clientID}:{$clientSecret}");
	}

}