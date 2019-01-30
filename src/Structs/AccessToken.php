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


use Carbon\Carbon;

class AccessToken {

	/**
	 * The leeway, in seconds, to say that a token is expired
	 */
	const LEEWAY = 5;

	protected $accessToken;
	protected $tokenType;
	protected $expiresIn;

	/**
	 * @var Carbon When was this token generated at?
	 */
	protected $generatedAt;

	public function __construct(array $generatedAccessToken, Carbon $generatedAt) {
		$this->accessToken = $generatedAccessToken['access_token'] ?? null;
		$this->tokenType = $generatedAccessToken['token_type'] ?? null;
		$this->expiresIn = $generatedAccessToken['expires_in'] ?? null;
		$this->generatedAt = $generatedAt;
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

	public function getGeneratedAt() : Carbon {
		return $this->generatedAt;
	}

	public function hasExpired() : bool {
		if($this->generatedAt === null) return true;
		return $this->generatedAt
			->addSeconds($this->getExpiresIn() - self::LEEWAY)
			->isFuture();
	}

}