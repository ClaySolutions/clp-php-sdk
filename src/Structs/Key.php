<?php
/**
 * clp-php-sdk
 * Key.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-31, 11:12
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class Key implements Arrayable {

	protected $id;
	protected $key_id;
	protected $key_number;
	protected $key_type;
	protected $accessor_id;
	protected $blocked;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->key_id = $apiResponse['key_id'] ?? null;
		$this->key_number = $apiResponse['key_number'] ?? null;
		$this->key_type = $apiResponse['key_type'] ?? null;
		$this->accessor_id = $apiResponse['accessor_id'] ?? null;
		$this->blocked = boolval($apiResponse['blocked'] ?? false);
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getKeyID(): ?string {
		return $this->key_id;
	}

	public function getKeyNumber(): ?string {
		return $this->key_number;
	}

	public function getKeyType(): ?string {
		return $this->key_type;
	}

	public function getAccessorID(): ?string {
		return $this->accessor_id;
	}

	public function isBlocked(): bool {
		return $this->blocked;
	}

	public function toArray() : array {
		return [
			'id' => $this->id,
			'key_id' => $this->key_id,
			'key_number' => $this->key_number,
			'key_type' => $this->key_type,
			'accessor_id' => $this->accessor_id,
			'blocked' => $this->blocked,
		];
	}

}