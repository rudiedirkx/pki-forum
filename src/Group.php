<?php

namespace App;

use Exception;

/**
 * @property string $name
 * @property string $private_key
 * @property string $public_key
 *
 * @property resource $pkey
 */
class Group extends Model {

	static public $_table = 'groups';

	public function __toString() {
		return $this->name;
	}

	public function encrypt( $data ) {
		if ( !openssl_public_encrypt($data, $output, $this->public_key) ) {
			throw new Exception(__METHOD__);
		}

		return base64_encode($output);
	}

	public function decrypt( $data ) {
		if ( !openssl_private_decrypt(base64_decode($data), $output, $this->pkey) ) {
			throw new Exception(__METHOD__);
		}

		return $output;
	}

}
