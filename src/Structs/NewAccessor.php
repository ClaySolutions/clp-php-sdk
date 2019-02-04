<?php
/**
 * clp-php-sdk
 * NewAccessor.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 10:09
 */

namespace Clay\CLP\Structs;


class NewAccessor {

	public $collection_id;
	public $blocked;
	public $override_privacy_mode;
	public $toggle_easy_office_mode;
	public $toggle_manual_office_mode;
	public $remote_access;

	public function __construct(?string $collectionID,
	                            bool $isBlocked = false,
	                            bool $canOverridePrivacyMode = false,
	                            bool $canToggleEasyOfficeMode = true,
	                            bool $canToggleManualOfficeMode = true,
	                            bool $canHaveRemoteAccess = true) {

		$this->collection_id = $collectionID;
		$this->blocked = $isBlocked;
		$this->override_privacy_mode = $canOverridePrivacyMode;
		$this->toggle_easy_office_mode = $canToggleEasyOfficeMode;
		$this->toggle_manual_office_mode = $canToggleManualOfficeMode;
		$this->remote_access = $canHaveRemoteAccess;

	}

}