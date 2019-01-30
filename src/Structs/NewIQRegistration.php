<?php
/**
 * clp-php-sdk
 * NewIQRegistrationon.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 11:19
 */

namespace Clay\CLP\Structs;


class NewIQRegistration {

	public $customer_reference;
	public $collection_id;
	public $time_zone;
	public $activation_code;
	public $subscribed;

	/**
	 * NewIQRegistration constructor.
	 * @param $customer_reference
	 * @param $collection_id
	 * @param $time_zone
	 * @param $activation_code
	 * @param $subscribed
	 */
	public function __construct(string $customer_reference, string $collection_id, string $time_zone, string $activation_code, bool $subscribed) {
		$this->customer_reference = $customer_reference;
		$this->collection_id = $collection_id;
		$this->time_zone = $time_zone;
		$this->activation_code = $activation_code;
		$this->subscribed = $subscribed;
	}


}