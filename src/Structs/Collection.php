<?php
/**
 * clp-php-sdk
 * Collection.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 11:30
 */

namespace Clay\CLP\Structs;


class Collection {

	protected $id;
	protected $customer_reference;
	protected $country_code;
	protected $sync_mkey;
	protected $masterkey_id;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->country_code = $apiResponse['country_code'] ?? null;
		$this->sync_mkey = $apiResponse['sync_mkey'] ?? null;
		$this->masterkey_id = $apiResponse['masterkey_id'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function getCountryCode(): ?string {
		return $this->country_code;
	}

	public function getSyncMKey(): ?string {
		return $this->sync_mkey;
	}

	public function getMasterKeyID(): ?string {
		return $this->masterkey_id;
	}

}