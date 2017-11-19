<?php

/** @return User */
function check_login() {
	if ( isset($_SESSION['pki_username'], $_SESSION['pki_password']) ) {
		if ( $user = check_user($_SESSION['pki_username'], $_SESSION['pki_password']) ) {
			return $user;
		}
	}

	return do_redirect('login');
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
