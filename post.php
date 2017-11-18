<?php

require 'inc.bootstrap.php';

$user = check_login();

include 'tpl.header.php';

$id = (int) @$_GET['id'];
$post = UserPost::first(['user_id' => $user->id, 'post_id' => $id]);

?>
<p><a href="index.php">Home</a></p>

<h1><?= html($post->decrypted_title) ?></h1>

<blockquote>
	<?= html($post->decrypted_body) ?>
</blockquote>

<pre><? print_r($post) ?></pre>

<?php

include 'tpl.footer.php';
