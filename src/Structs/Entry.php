<?php
/**
 * clp-php-sdk
 * Entry.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-05-02, 16:47
 */

namespace Clay\CLP\Structs;


class Entry {

	protected $id;
	protected $event_category;
	protected $event_detail;
	protected $utc_date_time;
	protected $local_date_time;
	protected $lock_id;
	protected $lock_customer_reference;
	protected $lock_mac_address;
	protected $accessor_id;
	protected $iq_id;
	protected $iq_customer_reference;
	protected $iq_mac_address;
	protected $iq_revision;
	protected $collection_id;
	protected $collection_customer_reference;
	protected $exit_requested;
	protected $access_by;
	protected $access_detail;

	public function __construct(array $apiData = []) {
		$this->id = $apiData['id'] ?? null;
		$this->event_category = $apiData['event_category'] ?? null;
		$this->event_detail = $apiData['event_detail'] ?? null;
		$this->utc_date_time = $apiData['utc_date_time'] ?? null;
		$this->local_date_time = $apiData['local_date_time'] ?? null;
		$this->lock_id = $apiData['lock_id'] ?? null;
		$this->lock_customer_reference = $apiData['lock_customer_reference'] ?? null;
		$this->lock_mac_address = $apiData['lock_mac_address'] ?? null;
		$this->accessor_id = $apiData['accessor_id'] ?? null;
		$this->iq_id = $apiData['iq_id'] ?? null;
		$this->iq_customer_reference = $apiData['iq_customer_reference'] ?? null;
		$this->iq_mac_address = $apiData['iq_mac_address'] ?? null;
		$this->iq_revision = $apiData['iq_revision'] ?? null;
		$this->collection_id = $apiData['collection_id'] ?? null;
		$this->collection_customer_reference = $apiData['collection_customer_reference'] ?? null;
		$this->exit_requested = boolval($apiData['exit_requested'] ?? false);
		$this->access_by = $apiData['access_by'] ?? null;
		$this->access_detail = $apiData['access_detail'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getEventCategory(): ?string {
		return $this->event_category;
	}

	public function getEventDetail(): ?string {
		return $this->event_detail;
	}

	public function getUtcDateTime(): ?string {
		return $this->utc_date_time;
	}

	public function getLocalDateTime(): ?string {
		return $this->local_date_time;
	}

	public function getLockID(): ?string {
		return $this->lock_id;
	}

	public function getLockCustomerReference(): ?string {
		return $this->lock_customer_reference;
	}

	public function getLockMacAddress(): ?string {
		return $this->lock_mac_address;
	}

	public function getAccessorID(): ?string {
		return $this->accessor_id;
	}

	public function getIQID(): ?string {
		return $this->iq_id;
	}

	public function getIQCustomerReference(): ?string {
		return $this->iq_customer_reference;
	}

	public function getIQMacAddress(): ?string {
		return $this->iq_mac_address;
	}

	public function getIQRevision(): ?string {
		return $this->iq_revision;
	}

	public function getCollectionID(): ?string {
		return $this->collection_id;
	}

	public function getCollectionCustomerReference(): ?string {
		return $this->collection_customer_reference;
	}

	public function isExitRequested(): bool {
		return $this->exit_requested;
	}

	public function getAccessBy(): ?string {
		return $this->access_by;
	}

	public function getAccessDetail(): ?string {
		return $this->access_detail;
	}



}