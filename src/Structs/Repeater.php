<?php
/**
 * clp-php-sdk
 * Repeater.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:27
 */

namespace Clay\CLP\Structs;


class Repeater extends IQHardware {

	protected $id;
	protected $iq_id;
	protected $repeater_id;
	protected $mac;
	protected $customer_reference;
	protected $online;
	protected $iq_link_state;
	protected $collection_id;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->iq_id = $apiResponse['iq_id'] ?? null;
		$this->repeater_id = $apiResponse['repeater_id'] ?? null;
		$this->mac = $apiResponse['mac'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->online = boolval($apiResponse['online'] ?? null);
		$this->iq_link_state = $apiResponse['iq_link_state'] ?? null;
		$this->collection_id = $apiResponse['collection_id'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getIQID(): ?string {
		return $this->iq_id;
	}

	public function getRepeaterID(): ?string {
		return $this->repeater_id;
	}

	public function getMacAddress(): ?string {
		return $this->mac;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function isOnline(): bool {
		return $this->online ?? false;
	}

	public function getIQLinkState(): ?string {
		return $this->iq_link_state;
	}

	public function getCollectionId(): ?string {
		return $this->collection_id;
	}



}