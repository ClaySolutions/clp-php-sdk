<?php
/**
 * clp-php-sdk
 * KeyPairGenerator.php
 *
 * Copyright (c) Clay Solutions BV
 * my-clay.com - 2019
 *
 * @author Aryel Tupinamba <aryel@my-clay.com>
 *
 * Created at: 2019-02-04, 17:00
 */

namespace Clay\CLP\Utilities;


use Clay\CLP\Structs\KeyPair;
use phpseclib\Crypt\RSA;

class KeyPairGenerator {

	public static function generate() : KeyPair {

		$rsa = new RSA();
		$keyPair = $rsa->createKey();

//		return new KeyPair(base64_encode($keyPair['publickey']), base64_encode($keyPair['privatekey']));

		return new KeyPair(
			'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEhdX2hxg7ORoSeIsgC0ZP4NLTXHUqDHyBRvtRZaJbR8QgJmfMBL9v7KtcII54YYc8OoIIRkSCN48IZzRFmIOaHw==',
			'tbdgGZcivMUgn6rccjgop1m37lIdF8xtXlFYQzBJK0E5ExjokxqE3h5IFF9tulJDroHpndy2oGEOEZsKX7UcN1+F/4arUehaDZg5cvi8w3mS9NF/jmdflT4GK72T66Z4GGXMTtO0SBiTV6gcL8553NMkFmt8CF6/sdeK6hXv9qy0tsMgFC2z7t9btOOFX02xJkhBEWSQAXfPdbgfw0Dd6RRPD3xf9S4kC+HrPxGljtU7wXiwwhS8oeUerb0rdP4KVTnhIVFdetqoqJRUYh9bWkRWDrv0A+WGIm9zw8vBq3OvOCW9xnM6qRS7Rm7VN7SqZtmlP7Svkq1oEKXGc56yQQ=='
		);
	}

}