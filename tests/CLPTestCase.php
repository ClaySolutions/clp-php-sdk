<?php
/**
 * clp-php-sdk
 * CLPTestCase.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-30, 15:21
 */

namespace Tests;


use Clay\CLP\Clients\CLPService;
use Clay\CLP\Clients\IdentityServerService;
use Clay\CLP\Contracts\HttpClient;
use Clay\CLP\Http\CurlHttpClient;
use Clay\CLP\Structs\OAuthParameters;

class CLPTestCase extends TestCase {

	/**
	 * @var CLPService $clp
	 */
	protected $clp;

	/**
	 * @var HttpClient $httpClient
	 */
	protected $httpClient;

	/**
	 * @var IdentityServerService
	 */
	protected $identityServer;


	public function setUp() {
		parent::setUp();

		$oauthParams = new OAuthParameters(
			$this->config->get('clp.client_id'),
			$this->config->get('clp.client_secret'),
			'hardware_api'
		);

		$this->identityServer = new IdentityServerService(
			$oauthParams,
			new CurlHttpClient(
				$this->config->get('clp.endpoints.identity_server', 'http://localhost/'),
				['Accept' => 'application/json']
			)
		);

		$this->httpClient = new CurlHttpClient(
			$this->config->get('clp.endpoints.api', 'http://localhost/'),
			['Accept' => 'application/json'],
			$this->identityServer
		);

		$this->clp = new CLPService($this->httpClient);
	}

}