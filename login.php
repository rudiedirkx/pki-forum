<?php

require 'inc.bootstrap.php';

check_login(false) and do_redirect('index');

if ( isset($_POST['username'], $_POST['password']) ) {
	if ( !check_user($_POST['username'], $_POST['password']) ) {
		return do_redirect('login', ['msg' => 'Invalid username/password']);
	}

	setcookie('pki_auth', do_encrypt(json_encode([$_POST['username'], $_POST['password']])));

	return do_redirect('index');
}

include 'tpl/header.php';

?>
<h1>Log in</h1>

<p><a href="register.php">Register</a></p>

<form method="post" action autocomplete="off">
	<p>Username: <input required name="username" autofocus /></p>
	<p>Password: <input required name="password" value="test" /></p>
	<p><button>Log in</button></p>
</form>

<?php

include 'tpl/footer.php';
