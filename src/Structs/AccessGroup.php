<?php
/**
 * clp-php-sdk
 * AccessGroup.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 13:32
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class AccessGroup implements Arrayable {

	protected $id;
	protected $customer_reference;
	protected $collection_id;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->collection_id = $apiResponse['collection_id'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function getCollectionID(): ?string {
		return $this->collection_id;
	}

	public function toArray() : array {
		return [
			'id' => $this->id,
			'customer_reference' => $this->customer_reference,
			'collection_id' => $this->collection_id,
		];
	}

}