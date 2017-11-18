<?php

require 'inc.bootstrap.php';

$g_user = check_login();

unset($_SESSION['pki_username'], $_SESSION['pki_password']);

return do_redirect('index');
