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
use Clay\CLP\Structs\AccessToken;
use Illuminate\Support\ServiceProvider;

class CLPServiceProvider extends ServiceProvider {

	const TOKEN_CACHE_KEY = 'clay/clp-php-sdk@1.0.13/auth_token';
	const TOKEN_CACHE_LEEWAY = 20;

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

				if(!$isCacheEnabled) {
					return $identityServer->provideAccessToken()->generateAuthorizationHeader();
				}

				$serializedCachedToken = $cache->get(self::TOKEN_CACHE_KEY, null);
				$cachedToken = AccessToken::unserialize($serializedCachedToken); /* @var $cachedToken \Clay\CLP\Structs\AccessToken */

				if(!is_null($cachedToken) && !$cachedToken->hasExpired()) {
					return $cachedToken->generateAuthorizationHeader();
				}

				$generatedToken = $identityServer->fetchAccessToken();

				$cacheTTL = $generatedToken->getExpiresIn() - self::TOKEN_CACHE_LEEWAY;
				$cache->put(self::TOKEN_CACHE_KEY, $generatedToken->serialize(), $cacheTTL);

				return $generatedToken->generateAuthorizationHeader();

			});

			return $client;

		});

	}

}