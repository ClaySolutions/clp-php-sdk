<?php
/**
 * clp-php-sdk
 * CLP.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-05, 12:22
 */

namespace Clay\CLP\Facades;


use Clay\CLP\APIs\AccessGroupAPI;
use Clay\CLP\APIs\AccessorAPI;
use Clay\CLP\APIs\CollectionAPI;
use Clay\CLP\APIs\IQAPI;
use Clay\CLP\APIs\LockAPI;
use Clay\CLP\APIs\RepeaterAPI;
use Clay\CLP\APIs\TagAPI;
use Clay\CLP\Clients\CLPClient;
use Illuminate\Support\Facades\Facade;

/**
 * Class CLP
 * @package Clay\CLP\Facades
 *
 * @method static IQAPI iq()
 * @method static LockAPI locks()
 * @method static RepeaterAPI repeaters()
 * @method static TagAPI tags()
 * @method static AccessorAPI accessors()
 * @method static CollectionAPI collections()
 * @method static AccessGroupAPI accessGroups()
 */
class CLP extends Facade {

	protected static function getFacadeAccessor() {
		return CLPClient::class;
	}

}