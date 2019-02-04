<?php
/**
 * clp-php-sdk
 * NewAccessGroup.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 13:39
 */

namespace Clay\CLP\Structs;


class NewAccessGroup {

	public $customer_reference;
	public $collection_id;

	public function __construct(?string $customer_reference = null,
	                            ?string $collection_id = null) {
		$this->customer_reference = $customer_reference;
		$this->collection_id = $collection_id;
	}

}