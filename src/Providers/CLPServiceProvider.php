<?php
/**
 * clp-php-sdk
 * CLPServiceProvider.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-05, 11:26
 */

namespace Clay\CLP\Providers;


use Clay\CLP\Clients\CLPClient;
use Clay\CLP\Clients\IdentityServerClient;
use Clay\CLP\Clients\VaultClient;
use Illuminate\Support\ServiceProvider;

class CLPServiceProvider extends ServiceProvider {

	const TOKEN_CACHE_KEY = 'clay/clp-php-sdk/auth_token';

	public function register() {

		$this->app->singleton(IdentityServerClient::class, function ($app) {
			return new IdentityServerClient($app->make('config'));
		});

		$this->app->singleton(VaultClient::class, function ($app) {
			return new VaultClient($app->make('config'));
		});

		$this->app->singleton(CLPClient::class, function ($app) {

			$config = $app->make('config'); /* @var $config \Illuminate\Contracts\Config\Repository */
			$cache = $app->make('cache'); /* @var $cache \Illuminate\Contracts\Cache\Repository */

			$isCacheEnabled = $config->get('clp.service.enable_token_cache', false);

			$client = new CLPClient($config);

			$identityServer = $app->make(IdentityServerClient::class); /* @var $identityServer \Clay\CLP\Clients\IdentityServerClient */

			$client->setAuthorizationHeaderProvider(function () use ($identityServer, $isCacheEnabled, $cache) {

				if($isCacheEnabled) {
					$authToken = $cache->get(self::TOKEN_CACHE_KEY, null); /* @var $authToken \Clay\CLP\Structs\AccessToken */

					if(!is_null($authToken)) {
						return $authToken->generateAuthorizationHeader();
					}
				}

				$authToken = $identityServer->provideAccessToken();

				if($isCacheEnabled) {
					$cache->put(self::TOKEN_CACHE_KEY, $authToken, $authToken->getExpiresIn());
				}

				return $authToken->generateAuthorizationHeader();

			});

			return $client;

		});

	}

}