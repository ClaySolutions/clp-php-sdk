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

	public function register() {

		$this->app->singleton(CLPClient::class, function ($app) {
			return new CLPClient($app->make('config'));
		});

		$this->app->singleton(IdentityServerClient::class, function ($app) {
			return new IdentityServerClient($app->make('config'));
		});

		$this->app->singleton(VaultClient::class, function ($app) {
			return new VaultClient($app->make('config'));
		});

	}

}