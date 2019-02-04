<?php
/**
 * clp-php-sdk
 * Tag.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 09:45
 */

namespace Clay\CLP\Structs;


class Tag {

	protected $id;
	protected $source_iq_id;
	protected $tag_number;
	protected $added_date;
	protected $collection_id;

	public function __construct(array $apiResponse = []) {
		$this->id = $apiResponse['id'] ?? null;
		$this->source_iq_id = $apiResponse['source_iq_id'] ?? null;
		$this->tag_number = $apiResponse['tag_number'] ?? null;
		$this->added_date = $apiResponse['added_date'] ?? null;
		$this->collection_id = $apiResponse['collection_id'] ?? null;
	}

	public function getID(): ?string {
		return $this->id;
	}

	public function getSourceIQID(): ?string {
		return $this->source_iq_id;
	}

	public function getTagNumber(): ?string {
		return $this->tag_number;
	}

	public function getAddedDate(): ?string {
		return $this->added_date;
	}

	public function getCollectionID(): ?string {
		return $this->collection_id;
	}



}