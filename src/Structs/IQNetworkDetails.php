<?php
/**
 * clp-php-sdk
 * IQNetworkDetails.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-06-05, 13:21
 */

namespace Clay\CLP\Structs;


class IQNetworkDetails {

	protected $rfLastUpdate;
	protected $rfChannel;
	protected $networkAdapters;

	public function __construct(array $apiData = []) {
		$this->rfLastUpdate = $apiData['rf_info']->request_date ?? null;
		$this->rfChannel = $apiData['rf_info']->channel ?? null;

		$this->networkAdapters = collect((array) ($apiData['network_details'] ?? []))
			->map(function($adapter, $type) {
				return new IQNetworkAdapter($type, (array) ($adapter ?? []));
			})
			->keyBy('type');
	}

	/**
	 * @return IQNetworkAdapter[]|\Illuminate\Support\Collection
	 */
	public function getNetworkAdapters() : \Illuminate\Support\Collection {
		return $this->networkAdapters;
	}

	public function hasM2MAdapter() : bool {
		return $this->networkAdapters->has(IQNetworkAdapter::TYPE_M2M);
	}

	public function getM2MAdapter() : ?IQNetworkAdapter {
		return $this->networkAdapters->get(IQNetworkAdapter::TYPE_WIFI, null);
	}

	public function hasEthernetAdapter() : bool {
		return $this->networkAdapters->has(IQNetworkAdapter::TYPE_ETHERNET);
	}

	public function getEthernetAdapter() : ?IQNetworkAdapter {
		return $this->networkAdapters->get(IQNetworkAdapter::TYPE_WIFI, null);
	}

	public function hasWifiAdapter() : bool {
		return $this->networkAdapters->has(IQNetworkAdapter::TYPE_WIFI);
	}

	public function getWifiAdapter() : ?IQNetworkAdapter {
		return $this->networkAdapters->get(IQNetworkAdapter::TYPE_WIFI, null);
	}

}