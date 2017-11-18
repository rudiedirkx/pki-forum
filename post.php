<?php

require 'inc.bootstrap.php';

$g_user = check_login();

$id = (int) @$_GET['id'];
$post = UserPost::first(['user_id' => $g_user->id, 'post_id' => $id]);
if ( !$post ) {
	return do_redirect('index', ['msg' => 'Post not found']);
}

if ( isset($_POST['invite_username']) ) {
	$user = get_user($_POST['invite_username']);
	if ( $user ) {
		if ( !UserPost::first(['user_id' => $user->id, 'post_id' => $id]) ) {
			$key = $g_user->decrypt($post->crypter);
			UserPost::insert([
				'user_id' => $user->id,
				'post_id' => $id,
				'crypter' => $user->encrypt($key),
			]);

			$post->recryptPost($key);
		}

		return do_redirect('post', ['id' => $id, 'msg' => 'User invited']);
	}

	return do_redirect('index', ['msg' => 'Invalid user']);
}

include 'tpl.header.php';

?>
<p><a href="index.php">Home</a></p>

<h1><?= html($post->decrypted_title) ?></h1>

<blockquote>
	<?= html($post->decrypted_body) ?>
</blockquote>

<form method="post">
	<p>Username: <input name="invite_username" /></p>
	<p><button>Invite user</button>
</form>

<pre><? print_r($post) ?></pre>

<?php

include 'tpl.footer.php';
