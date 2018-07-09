<?php

require 'inc.bootstrap.php';

$g_user = check_login();

setcookie('pki_auth', '', 1);

return do_redirect('index');
