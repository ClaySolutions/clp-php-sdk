<?php
/**
 * clp-php-sdk
 * CLPClient.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-01-28, 16:05
 */

namespace Clay\CLP\Clients;

use Clay\CLP\APIs\IQAPI;
use Clay\CLP\APIs\LockAPI;
use Clay\CLP\APIs\RepeaterAPI;
use Clay\CLP\APIs\TagAPI;
use Clay\CLP\Utilities\AbstractAPI;
use Clay\CLP\Utilities\AbstractHttpClient;
use Illuminate\Contracts\Config\Repository;

class CLPClient extends AbstractHttpClient {

	/**
	 * The API to interact with IQs.
	 * @return IQAPI
	 */
	public function iqs() : AbstractAPI {
		return IQAPI::getInstance($this);
	}

	/**
	 * The API to interact with Locks.
	 * @return LockAPI
	 */
	public function locks() : AbstractAPI {
		return LockAPI::getInstance($this);
	}

	/**
	 * The API to interact with Repeaters.
	 * @return RepeaterAPI
	 */
	public function repeaters() : AbstractAPI {
		return RepeaterAPI::getInstance($this);
	}

	/**
	 * The API to interact with Tags.
	 * @return TagAPI
	 */
	public function tags() : AbstractAPI {
		return TagAPI::getInstance($this);
	}

	public function getEndpointBaseURL(): string {
		return $this->config->get('clp.endpoints.api');
	}
}