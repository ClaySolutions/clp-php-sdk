<?php
namespace Clay\CLP\Structs;

final class OAuthParameters {

	/**
	 * @property-read string
	 */
	public $clientId;

	/**
	 * @property-read string
	 */
	public $clientSecret;

	/**
	 * @property-read string
	 */
	public $scope;

	/**
	 * OAuthParameters constructor.
	 * @param $clientId
	 * @param $clientSecret
	 * @param $scope
	 */
	public function __construct(string $clientId, string $clientSecret, string $scope) {
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->scope = $scope;
	}


}