<?php
/**
 * clp-php-sdk
 * Lock.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-30, 15:14
 */

namespace Clay\CLP\Structs;


class Lock extends IQHardware {

	protected $id;
	protected $iq = null;
	protected $repeater = null;
	protected $mac;
	protected $customer_reference;
	protected $locked_state;
	protected $lock_type;
	protected $online = false;
	protected $iq_link_state;
	protected $tag_registration_state;
	protected $battery_level;
	protected $left_open_alarm = false;
	protected $intrusion_alarm = false;
	protected $easy_office_mode_schedule = null;
	protected $collection_id = null;
	protected $privacy_mode = false;

	public function __construct(array $apiResponse) {
		$this->id = $apiResponse['id'] ?? null;
		$this->iq = $apiResponse['iq'] ? ((object) $apiResponse['iq']) : null;
		$this->repeater = $apiResponse['repeater'] ? ((object) $apiResponse['repeater']) : null;;
		$this->mac = $apiResponse['mac'] ?? null;
		$this->customer_reference = $apiResponse['customer_reference'] ?? null;
		$this->locked_state = $apiResponse['locked_state'] ?? null;
		$this->lock_type = $apiResponse['lock_type'] ?? null;
		$this->online = boolval($apiResponse['online'] ?? null);
		$this->iq_link_state = $apiResponse['iq_link_state'] ?? null;
		$this->tag_registration_state = $apiResponse['tag_registration_state'] ?? null;
		$this->battery_level = $apiResponse['battery_level'] ?? null;
		$this->left_open_alarm = boolval($apiResponse['left_open_alarm'] ?? null);
		$this->intrusion_alarm = boolval($apiResponse['intrusion_alarm'] ?? null);
		$this->easy_office_mode_schedule = boolval($apiResponse['easy_office_mode_schedule'] ?? null);
		$this->collection_id = $apiResponse['collection_id'] ?? null;
		$this->privacy_mode = boolval($apiResponse['privacy_mode'] ?? null);
	}

	public function getType() : string {
		return 'lock';
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getIQ(): ?object {
		return $this->iq;
	}

	public function getRepeater(): ?object {
		return $this->repeater;
	}

	public function getMacAddress(): ?string {
		return $this->mac;
	}

	public function getCustomerReference(): ?string {
		return $this->customer_reference;
	}

	public function getLockedState(): ?string {
		return $this->locked_state;
	}

	public function getLockType(): ?string {
		return $this->lock_type;
	}

	public function isOnline(): bool {
		return $this->online;
	}

	public function getIQLinkState(): ?string {
		return $this->iq_link_state;
	}

	public function getTagRegistrationState(): ?string {
		return $this->tag_registration_state;
	}

	public function getBatteryLevel(): ?string {
		return $this->battery_level;
	}

	public function isLeftOpenAlarmEnabled(): bool {
		return $this->left_open_alarm;
	}

	public function isIntrusionAlarmEnabled(): bool {
		return $this->intrusion_alarm;
	}

	public function getEasyOfficeModeSchedule(): ?bool {
		return $this->easy_office_mode_schedule;
	}

	public function getCollectionID(): ?string {
		return $this->collection_id;
	}

	public function isPrivacyModeEnabled(): bool {
		return $this->privacy_mode;
	}

}