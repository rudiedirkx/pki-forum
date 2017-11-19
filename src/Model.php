<?php

namespace App;

use db_generic_model;

/**
 * @property int id
 */
class Model extends db_generic_model {

	/** @return string */
	static public function _encrypt( $key, $data ) {
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = openssl_random_pseudo_bytes($iv_size);
		return base64_encode($iv . openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
	}

	/** @return string */
	static public function _decrypt( $key, $data ) {
		$data = base64_decode($data);
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($data, 0, $iv_size);
		$data = substr($data, $iv_size);
		return rtrim(openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv), "\0");
	}

	/** @return string[] */
	static public function _pkey( $passphrase ) {
		$pkey = openssl_pkey_new(['digest_alg' => 'sha512', 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
		openssl_pkey_export($pkey, $privkey, $passphrase);
		$details = openssl_pkey_get_details($pkey);
		$pubkey = $details['key'];

		return [$privkey, $pubkey];
	}

}
