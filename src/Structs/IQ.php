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


class IQ {

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

	public function getID() {
		return $this->id;
	}
	public function getMacAddress() {
		return $this->mac;
	}
	public function getOperator() {
		return $this->operator;
	}
	public function getState() {
		return $this->state;
	}
	public function isRestoreRequired() {
		return $this->restore_required;
	}
	public function getResetDate() {
		return $this->reset_date;
	}
	public function isOnline() {
		return $this->online;
	}
	public function getDataSyncState() {
		return $this->data_sync_state;
	}
	public function getSignalStrength() {
		return $this->signal_strength;
	}
	public function getCollectionID() {
		return $this->collection_id;
	}
	public function getRevision() {
		return $this->revision;
	}
	public function getCustomerReference() {
		return $this->customer_reference;
	}
	public function getTimezone() {
		return $this->time_zone;
	}
	public function isSubscribed() {
		return $this->subscribed;
	}
	public function isOTPEnabled() {
		return $this->otp_enabled;
	}
	public function isLEDEnabled() {
		return $this->led_enabled;
	}

}