<?php

function check_login() {
	if ( isset($_SESSION['pki_username'], $_SESSION['pki_password']) ) {
		if ( $user = check_user($_SESSION['pki_username'], $_SESSION['pki_password']) ) {
			return $user;
		}
	}

	return do_redirect('login');
}

function check_user( $username, $password ) {
	global $db;
	$user = get_user($username);
	if ( $user ) {
		$user->pkey = openssl_pkey_get_private($user->private_key, $password);
		if ( $user->pkey ) {
			return $user;
		}
	}

	return false;
}

function get_user( $username ) {
	global $db;
	return User::first(['username' => $username]);
}

function get_rand( $length = 40 ) {
	$chars = array_merge(range('A', 'Z'), range(0, 9), range('a', 'z'));
	$rand = '';
	while ( strlen($rand) < $length ) {
		$rand .= $chars[ array_rand($chars) ];
	}

	return $rand;
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
