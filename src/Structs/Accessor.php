<?php
/**
 * clp-php-sdk
 * Accessor.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 10:05
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;

class Accessor implements Arrayable {

	protected $id;
	protected $remote_access;
	protected $blocked;
	protected $override_privacy_mode;
	protected $toggle_easy_office_mode;
	protected $toggle_manual_office_mode;
	protected $collection_id;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->remote_access = $apiResponse['remote_access'] ?? null;
		$this->blocked = boolval($apiResponse['blocked'] ?? null);
		$this->override_privacy_mode = boolval($apiResponse['override_privacy_mode'] ?? null);
		$this->toggle_easy_office_mode = boolval($apiResponse['toggle_easy_office_mode'] ?? null);
		$this->toggle_manual_office_mode = boolval($apiResponse['toggle_manual_office_mode'] ?? null);
		$this->collection_id = $apiResponse['collection_id'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function hasRemoteAccess(): ?string {
		return $this->remote_access;
	}

	public function isBlocked(): bool {
		return $this->blocked;
	}

	public function canOverridePrivacyMode(): bool {
		return $this->override_privacy_mode;
	}

	public function canToggleEasyOfficeMode(): bool {
		return $this->toggle_easy_office_mode;
	}

	public function canToggleManualOfficeMode(): bool {
		return $this->toggle_manual_office_mode;
	}

	public function getCollectionID(): ?string {
		return $this->collection_id;
	}

	public function toArray() : array {
		return [
			'id' => $this->id,
			'remote_access' => $this->remote_access,
			'blocked' => boolval($this->blocked ?? false),
			'override_privacy_mode' => boolval($this->override_privacy_mode ?? false),
			'toggle_easy_office_mode' => boolval($this->toggle_easy_office_mode ?? false),
			'toggle_manual_office_mode' => boolval($this->toggle_manual_office_mode ?? false),
			'collection_id' => $this->collection_id,
		];
	}

}