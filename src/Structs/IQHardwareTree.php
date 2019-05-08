<?php
/**
 * clp-php-sdk
 * IQHardwareTree.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-30, 14:46
 */

namespace Clay\CLP\Structs;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class IQHardwareTree implements Arrayable {

	protected $iqID;
	protected $tree;

	public function __construct(string $iqID) {
		$this->iqID = strval($iqID ?? null);
		$this->tree = new Collection();
	}

	public function getIQID() : ?string {
		return $this->iqID;
	}

	public function addHardware(IQHardware $hardware) : self {
		if($this->tree->contains('id', $hardware->getID())) {
			return $this;
		}

		$this->tree->push($hardware);

		return $this;
	}

	public function removeHardware(IQHardware $hardware) : self {

		$this->tree = $this->tree->filter(function ($leaf) use ($hardware) { /* @var $leaf \Clay\CLP\Structs\IQHardware */
			return ($leaf->getID() !== $hardware->getID());
		});

		return $this;

	}

	public function getAllHardware() : Collection {
		return $this->tree;
	}

	public function toArray() : array {
		return [
			'iqID' => $this->iqID,
			'tree' => $this->tree
				->map(function (IQHardware $hardware) {
					return $hardware->toArray();
				})
				->toArray(),
		];
	}

}