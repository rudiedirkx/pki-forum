<?php

require 'inc.bootstrap.php';

if ( isset($_POST['username'], $_POST['password']) ) {
	if ( get_user($_POST['username']) ) {
		return do_redirect('register', ['msg' => 'Username exists']);
	}

	$pkey = openssl_pkey_new(['digest_alg' => 'sha512', 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
	openssl_pkey_export($pkey, $privkey, $_POST['password']);
	$details = openssl_pkey_get_details($pkey);
	$pubkey = $details['key'];

	$db->insert('users', [
		'username' => $_POST['username'],
		'public_key' => $pubkey,
		'private_key' => $privkey,
	]);

	return do_redirect('login', ['msg' => 'User created']);
}

include 'tpl.header.php';

?>
<h1>Register</h1>

<p><a href="login.php">Log in</a></p>

<form method="post" action>
	<p>Username: <input name="username" autofocus /></p>
	<p>Password: <input name="password" /></p>
	<p><button>Save</button></p>
</form>

<?php

include 'tpl.footer.php';
