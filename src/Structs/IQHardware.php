<?php
/**
 * clp-php-sdk
 * IQHardware.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 11:51
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class IQHardware implements Arrayable {

	protected $id;
	protected $parent_id;
	protected $hardware_type;
	protected $mac;
	protected $customer_reference;
	protected $is_online;
	protected $is_attached;
	protected $battery_state;

	public function __construct(array $apiResponse) {
		$this->id = $apiResponse['id'] ?? null;
		$this->parent_id = $apiResponse['parent_id'] ?? null;
		$this->hardware_type = $apiResponse['hardware_type'] ?? null;
		$this->mac = $apiResponse['mac'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->is_online = boolval($apiResponse['is_online'] ?? false);
		$this->is_attached = boolval($apiResponse['is_attached'] ?? false);
		$this->battery_state = $apiResponse['battery_state'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getParentID(): string {
		return $this->parent_id;
	}

	public function getType(): string {
		return $this->hardware_type;
	}

	public function getMacAddress(): ?string {
		return $this->mac;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function isOnline(): bool {
		return $this->is_online;
	}

	public function isAttached(): bool {
		return $this->is_attached;
	}

	public function getBatteryState(): string {
		return $this->battery_state;
	}

	public function toArray() : array {
		return [
			'id' => $this->id,
			'parent_id' => $this->parent_id,
			'hardware_type' => $this->hardware_type,
			'mac' => $this->mac,
			'customer_reference' => $this->customer_reference,
			'is_online' => $this->is_online,
			'is_attached' => $this->is_attached,
			'battery_state' => $this->battery_state,
		];
	}

}