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


use Clay\CLP\Clients\CLPService;
use Clay\CLP\Clients\IdentityServerService;
use Clay\CLP\Clients\VaultClient;
use Clay\CLP\Contracts\HttpClient;
use Clay\CLP\Http\CurlHttpClient;
use Clay\CLP\Structs\AccessToken;
use Clay\CLP\Structs\OAuthParameters;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

final class CLPServiceProvider extends ServiceProvider {

	private const TOKEN_CACHE_KEY = 'clay/clp-php-sdk@1.1.0/auth_token';
	private const TOKEN_CACHE_LEEWAY = 20;

	public function register(): void {

		$config = $this->app->make('config'); /* @var $config Repository */

		$this->app->singleton('clp_identity_client', static function ($app) use ($config) {
			return new CurlHttpClient(
				$config->get('clp.endpoints.identity_server'),
				['Accept' => 'application/json']
			);
		});

		$this->app->singleton(IdentityServerService::class, static function ($app) use ($config) {
			return new IdentityServerService(
				new OAuthParameters(
					$config->get('clp.client_id'),
					$config->get('clp.client_secret'),
					'hardware_api'
				),
				$this->app->make('clp_identity_client')
			);
		});

		$this->app->singleton('clp_api_client', static function ($app) use ($config) {
			return new CurlHttpClient(
				$config->get('clp.endpoints.api'),
				['Accept' => 'application/json'],
				$this->app->make(IdentityServerService::class)
			);
		});

		$this->app->singleton(VaultClient::class, function ($app) {
			return new VaultClient($app->make('config'));
		});

		$this->app->singleton(CLPService::class, function ($app) use ($config) {

			$cache = $app->make('cache'); /* @var $cache \Illuminate\Contracts\Cache\Repository */

			$isCacheEnabled = $config->get('clp.service.enable_token_cache', false);

			$client = new CLPService($config);

			$identityServer = $app->make(IdentityServerService::class); /* @var $identityServer \Clay\CLP\Clients\IdentityServerService */

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