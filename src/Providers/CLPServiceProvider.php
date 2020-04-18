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
use \Illuminate\Contracts\Config\Repository as ConfigRepository;
use \Illuminate\Contracts\Cache\Repository as CacheRepository;

class CLPServiceProvider extends ServiceProvider {

	public const TOKEN_CACHE_KEY = 'clay/clp-php-sdk@1.0.13/auth_token';
	public const TOKEN_CACHE_LEEWAY = 20;

	public function register(): void {

		$this->app->singleton(IdentityServerClient::class, function ($app) {
			return new IdentityServerClient($this->app->make(ConfigRepository::class));
		});

		$this->app->singleton(VaultClient::class, function ($app) {
			return new VaultClient($this->app->make(ConfigRepository::class));
		});

		$this->app->singleton(CLPClient::class, function ($app) {

			$cache = $this->app->make(CacheRepository::class);
			$config = $this->app->make(ConfigRepository::class);

			$isCacheEnabled = $config->get('clp.service.enable_token_cache', false);

			$client = new CLPClient($config);

			$identityServer = $this->app->make(IdentityServerClient::class); /* @var $identityServer \Clay\CLP\Clients\IdentityServerClient */

			$client->setAuthorizationHeaderProvider(static function () use ($identityServer, $isCacheEnabled, $cache) {

				if(!$isCacheEnabled) {
					return $identityServer->provideAccessToken()->generateAuthorizationHeader();
				}

				$serializedCachedToken = $cache->get(self::TOKEN_CACHE_KEY, null);
				$cachedToken = AccessToken::unserialize($serializedCachedToken); /* @var $cachedToken \Clay\CLP\Structs\AccessToken */

				if($cachedToken !== null && !$cachedToken->hasExpired()) {
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
