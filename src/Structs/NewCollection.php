<?php
/**
 * clp-php-sdk
 * NewCollection.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 11:34
 */

namespace Clay\CLP\Structs;


class NewCollection {

	public $customer_reference;
	public $country_code;
	public $sync_mkey;
	public $masterkey_id;

	public function __construct(?string $customer_reference, string $country_code, ?string $sync_mkey, ?string $masterkey_id) {
		$this->customer_reference = $customer_reference;
		$this->country_code = $country_code;
		$this->sync_mkey = $sync_mkey;
		$this->masterkey_id = $masterkey_id;
	}

}