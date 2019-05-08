<?php
/**
 * clp-php-sdk
 * IQ.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-29, 10:10
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class IQ implements Arrayable {

	protected $id;
	protected $mac;
	protected $operator;
	protected $state;
	protected $restore_required;
	protected $reset_date;
	protected $online;
	protected $data_sync_state;
	protected $signal_strength;
	protected $collection_id;
	protected $revision;
	protected $customer_reference;
	protected $time_zone;
	protected $subscribed;
	protected $otp_enabled;
	protected $led_enabled;

	public function __construct(array $apiResponse) {
		$this->id = $apiResponse['id'] ?? null;
		$this->mac = $apiResponse['mac'] ?? null;
		$this->operator = $apiResponse['operator'] ?? null;
		$this->state = $apiResponse['state'] ?? null;
		$this->restore_required = boolval($apiResponse['restore_required'] ?? false);
		$this->reset_date = $apiResponse['reset_date'] ?? null;
		$this->online = boolval($apiResponse['online'] ?? false);
		$this->data_sync_state = $apiResponse['data_sync_state'] ?? null;
		$this->signal_strength = $apiResponse['signal_strength'] ? intval($apiResponse['signal_strength']) : null;
		$this->collection_id = $apiResponse['collection_id'] ?? null;
		$this->revision = $apiResponse['revision'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->time_zone = $apiResponse['time_zone'] ?? null;
		$this->subscribed = boolval($apiResponse['subscribed'] ?? false);
		$this->otp_enabled = boolval($apiResponse['otp_enabled'] ?? false);
		$this->led_enabled = boolval($apiResponse['led_enabled'] ?? false);
	}

	public function getID() : ?string {
		return $this->id;
	}

	public function getMacAddress() : ?string {
		return $this->mac;
	}

	public function getOperator() : ?string {
		return $this->operator;
	}

	public function getState() : ?string {
		return $this->state;
	}

	public function isRestoreRequired() : bool {
		return $this->restore_required;
	}

	public function getResetDate() : ?string {
		return $this->reset_date;
	}

	public function isOnline() : bool {
		return $this->online;
	}

	public function getDataSyncState() : ?string {
		return $this->data_sync_state;
	}

	public function getSignalStrength() : ?string {
		return $this->signal_strength;
	}

	public function getCollectionID() : ?string {
		return $this->collection_id;
	}

	public function getRevision() : ?string {
		return $this->revision;
	}

	public function getCustomerReference() : ?string {
		return $this->customer_reference;
	}

	public function getTimezone() : ?string {
		return $this->time_zone;
	}

	public function isSubscribed() : bool {
		return $this->subscribed;
	}

	public function isOTPEnabled() : bool {
		return $this->otp_enabled;
	}

	public function isLEDEnabled() : bool {
		return $this->led_enabled;
	}


	public function toArray() : array {
		return [
			'id' => $this->id,
			'mac' => $this->mac,
			'operator' => $this->operator,
			'state' => $this->state,
			'restore_required' => $this->restore_required,
			'reset_date' => $this->reset_date,
			'online' => $this->online,
			'data_sync_state' => $this->data_sync_state,
			'signal_strength' => $this->signal_strength,
			'collection_id' => $this->collection_id,
			'revision' => $this->revision,
			'customer_reference' => $this->customer_reference,
			'time_zone' => $this->time_zone,
			'subscribed' => $this->subscribed,
			'otp_enabled' => $this->otp_enabled,
			'led_enabled' => $this->led_enabled,
		];
	}

}