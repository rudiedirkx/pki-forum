<?php

require 'inc.bootstrap.php';

if ( isset($_POST['username'], $_POST['password']) ) {
	if ( get_user($_POST['username']) ) {
		return do_redirect('register', ['msg' => 'Username exists']);
	}

	list($privkey, $pubkey) = Model::_pkey($_POST['password']);

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

<form method="post" action autocomplete="off">
	<p>Username: <input required name="username" autofocus /></p>
	<p>Password: <input required name="password" value="test" /></p>
	<p><button>Save</button></p>
</form>

<?php

include 'tpl.footer.php';
