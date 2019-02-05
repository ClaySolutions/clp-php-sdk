<?php
/**
 * clp-php-sdk
 * KeyPair.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 17:00
 */

namespace Clay\CLP\Structs;


class KeyPair {

	protected $publicKey;
	protected $privateKey;

	public function __construct(string $publicKey, string $privateKey) {
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}

	public function getPublicKey(): string {
		return $this->publicKey;
	}

	public function getPrivateKey(): string {
		return $this->privateKey;
	}

}