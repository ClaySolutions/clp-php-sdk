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


class IQHardware {

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

	public function getId(): string {
		return $this->id;
	}

	public function getParentId(): string {
		return $this->parent_id;
	}

	public function getHardwareType(): string {
		return $this->hardware_type;
	}

	public function getMac(): string {
		return $this->mac;
	}

	public function getCustomerReference(): string {
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

}