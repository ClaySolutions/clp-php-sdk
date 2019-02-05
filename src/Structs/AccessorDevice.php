<?php
/**
 * clp-php-sdk
 * AccessorDevice.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 16:43
 */

namespace Clay\CLP\Structs;


class AccessorDevice {

	protected $id;
	protected $customer_reference;
	protected $state;
	protected $mkey_id;
	protected $mkey_expiry_date;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->state = $apiResponse['state'] ?? null;
		$this->mkey_id = $apiResponse['mkey']->id ?? null;
		$this->mkey_expiry_date = $apiResponse['mkey']->expiry_date ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function getState(): ?string {
		return $this->state;
	}

	public function getMobileKeyID() : ?string {
		return $this->mkey_id;
	}

	public function getMobileKeyExpiryDate() : ?string {
		return $this->mkey_expiry_date;
	}

}