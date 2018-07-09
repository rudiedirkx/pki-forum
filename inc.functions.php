<?php

use App\Model;
use App\User;

/** @return User */
function check_login( $redirect = true ) {
	if ( isset($_COOKIE['pki_auth']) ) {
		if ( $auth = do_decrypt($_COOKIE['pki_auth']) ) {
			list($username, $password) = json_decode($auth);
			if ( $user = check_user($username, $password) ) {
				return $user;
			}
		}
	}

	$redirect and do_redirect('login');
}

/** @return User */
function check_user( $username, $password ) {
	$user = get_user($username);
	if ( $user ) {
		$user->pkey = openssl_pkey_get_private($user->private_key, $password);
		if ( $user->pkey ) {
			return $user;
		}
	}

	return false;
}

/** @return User */
function get_user( $username ) {
	return User::first(['username' => $username]);
}

function get_rand() {
	$chars = array_merge(range('A', 'Z'), range(0, 9), range('a', 'z'));
	$length = 40;

	$rand = '';
	while ( strlen($rand) < $length ) {
		$rand .= $chars[ array_rand($chars) ];
	}

	return $rand;
}

function do_encrypt( $data ) {
	$iv_size = openssl_cipher_iv_length('AES-256-CBC');
	$iv = openssl_random_pseudo_bytes($iv_size);
	return base64_encode($iv . openssl_encrypt($data, 'AES-256-CBC', PKI_SECRET, 0, $iv));
}

function do_decrypt( $data ) {
	$data = base64_decode($data);
	$iv_size = openssl_cipher_iv_length('AES-256-CBC');
	$iv = substr($data, 0, $iv_size);
	$data = substr($data, $iv_size);
	return rtrim(openssl_decrypt($data, 'AES-256-CBC', PKI_SECRET, 0, $iv), "\0");
}

function do_redirect( $path, $query = null ) {
	$fragment = '';
	if ( is_int($p = strpos($path, '#')) ) {
		$fragment = substr($path, $p);
		$path = substr($path, 0, $p);
	}

	$query = $query ? '?' . http_build_query($query) : '';
	$location = $path . '.php' . $query . $fragment;
	header('Location: ' . $location);
	exit;
}

function html( $text ) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function html_options( $options, $selected = null, $empty = '' ) {
	$selected = (array) $selected;

	$html = '';
	$empty && $html .= '<option value="">' . $empty;
	foreach ( $options AS $value => $label ) {
		if ( $label instanceof Model ) {
			$value = $label->id;
		}

		$isSelected = in_array($value, $selected) ? ' selected' : '';
		$html .= '<option value="' . html($value) . '" ' . $isSelected . '>' . html($label) . '</option>';
	}
	return $html;
}
