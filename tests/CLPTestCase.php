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


use Clay\CLP\Clients\CLPClient;
use Clay\CLP\Clients\IdentityServerClient;

class CLPTestCase extends TestCase {

	/**
	 * @var CLPClient $client
	 */
	protected $client;

	/**
	 * @var IdentityServerClient
	 */
	protected $identityServer;


	public function setUp() {
		parent::setUp();

		$this->identityServer = new IdentityServerClient($this->config);

		$this->client = new CLPClient($this->config);
		$this->client->setAuthorizationHeaderProvider(function () {
			return $this->identityServer->provideAccessToken()->generateAuthorizationHeader();
		});
	}

}