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
use Illuminate\Contracts\Support\Arrayable;
use Serializable;

class AccessToken implements Arrayable {

	/**
	 * The leeway, in seconds, to say that a token is expired
	 */
	const LEEWAY = 20;

	protected $accessToken;
	protected $tokenType;
	protected $expiresIn;

	/**
	 * @var Carbon When was this token generated at?
	 */
	protected $generatedAt;

	public function __construct(array $generatedAccessToken, Carbon $generatedAt) {
		$this->accessToken = $generatedAccessToken['access_token'] ?? null;
		$this->tokenType = $generatedAccessToken['token_type'] ?? null ;
		$this->expiresIn = $generatedAccessToken['expires_in'] ?? null;
		$this->generatedAt = $generatedAt;
	}

	public function getAccessToken() : ?string {
		return $this->accessToken;
	}

	public function getTokenType() : ?string {
		return $this->tokenType;
	}

	public function getExpiresIn() : int {
		return $this->expiresIn ? intval($this->expiresIn) : 0;
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
			->addSeconds(($this->getExpiresIn() ?? 0) - self::LEEWAY)
			->isPast();
	}

	public function toArray() : array {
		return [
			'accessToken' => $this->accessToken,
			'tokenType' => $this->tokenType,
			'expiresIn' => $this->expiresIn ?? 0,
			'generatedAt' => $this->generatedAt ? $this->generatedAt->toDateTimeString() : null,
		];
	}

	public function serialize() : string {
		return json_encode($this->toArray());
	}

	public static function unserialize(?string $serialized) : ?self {

		if(is_null($serialized)) return null;
		if(!$serialized) return null;

		$payload = json_decode($serialized, true);

		if(!$payload) return null;
		if(!isset($payload['accessToken'])) return null;

		$generatedAt = Carbon::parse($payload['generatedAt']);
		$tokenProperties = [
			'access_token' => $payload['accessToken'],
			'token_type' => $payload['tokenType'] ?? 'Bearer',
			'expires_in' => intval($payload['expiresIn']) ?? 0,
		];

		return new self($tokenProperties, $generatedAt);
	}
}