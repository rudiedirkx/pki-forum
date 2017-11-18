<?php

require 'inc.bootstrap.php';

if ( isset($_POST['username'], $_POST['password']) ) {
	if ( !check_user($_POST['username'], $_POST['password']) ) {
		return do_redirect('login', ['msg' => 'Invalid username/password']);
	}

	$_SESSION['pki_username'] = $_POST['username'];
	$_SESSION['pki_password'] = $_POST['password'];

	return do_redirect('index');
}

include 'tpl.header.php';

?>
<h1>Log in</h1>

<p><a href="register.php">Register</a></p>

<form method="post" action>
	<p>Username: <input name="username" autofocus /></p>
	<p>Password: <input name="password" /></p>
	<p><button>Log in</button></p>
</form>

<?php

include 'tpl.footer.php';
