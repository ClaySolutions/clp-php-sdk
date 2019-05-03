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

use Clay\CLP\APIs\AccessGroupAPI;
use Clay\CLP\APIs\AccessorAPI;
use Clay\CLP\APIs\CollectionAPI;
use Clay\CLP\APIs\EntriesAPI;
use Clay\CLP\APIs\IncidentsAPI;
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

	/**
	 * The API to interact with Accessors.
	 * @return AccessorAPI
	 */
	public function accessors() : AbstractAPI {
		return AccessorAPI::getInstance($this);
	}

	/**
	 * The API to interact with Collections.
	 * @return CollectionAPI
	 */
	public function collections() : AbstractAPI {
		return CollectionAPI::getInstance($this);
	}

	/**
	 * The API to interact with Access Groups.
	 * @return AccessGroupAPI
	 */
	public function accessGroups() : AbstractAPI {
		return AccessGroupAPI::getInstance($this);
	}

	/**
	 * The API to interact with logged entries
	 * @return EntriesAPI
	 */
	public function entries() : AbstractAPI {
		return EntriesAPI::getInstance($this);
	}

	/**
	 * The API to interact with logged incidents
	 * @return IncidentsAPI
	 */
	public function incidents() : AbstractAPI {
		return IncidentsAPI::getInstance($this);
	}

	public function getEndpointBaseURL(): string {
		return $this->config->get('clp.endpoints.api');
	}
}