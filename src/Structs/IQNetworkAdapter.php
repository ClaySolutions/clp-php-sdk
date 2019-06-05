<?php
/**
 * clp-php-sdk
 * IQNetworkAdapter.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-06-05, 13:21
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class IQNetworkAdapter implements Arrayable {

	const TYPE_UNKNOWN = "unknown";
	const TYPE_M2M = "m2m";
	const TYPE_ETHERNET = "ethernet";
	const TYPE_WIFI = "wifi";

	public $type;
	public $priority;
	public $status;
	public $macAddress;

	public function __construct(string $type = self::TYPE_UNKNOWN, array $apiData = []) {
		$this->type = $type ?? self::TYPE_UNKNOWN;
		$this->priority = $apiData['priority'] ?? null;
		$this->status = $apiData['status'] ?? null;
		$this->macAddress = $apiData['mac_address'] ?? null;
	}

	public function toArray() {
		return [
			'type' => $this->type,
			'priority' => $this->priority,
			'status' => $this->status,
			'mac_address' => $this->macAddress,
		];
	}


}