<?php
/**
 * clp-php-sdk
 * AccessToken.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:33
 */

namespace Clay\CLP\Structs;


class AccessToken {

	protected $accessToken;
	protected $tokenType;
	protected $expiresIn;

	public function __construct(array $generatedAccessToken) {
		$this->accessToken = $generatedAccessToken['access_token'] ?? null;
		$this->tokenType = $generatedAccessToken['token_type'] ?? null;
		$this->expiresIn = $generatedAccessToken['expires_in'] ?? null;
	}

	public function getAccessToken() : ?string {
		return $this->accessToken;
	}

	public function getTokenType() : ?string {
		return $this->tokenType;
	}

	public function getExpiresIn() : ?int {
		return $this->expiresIn ? intval($this->expiresIn) : null;
	}

	public function generateAuthorizationHeader() : string {
		return "{$this->getTokenType()} {$this->getAccessToken()}";
	}

}