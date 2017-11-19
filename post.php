<?php

use App\Post;
use App\UserPost;

require 'inc.bootstrap.php';

$g_user = check_login();

$id = (int) @$_GET['id'];
$post = Post::get($g_user, $id);
if ( !$post ) {
	return do_redirect('index', ['msg' => 'Post not found']);
}

if ( isset($_POST['invite_user']) ) {
	$user = get_user($_POST['invite_user']);
	if ( $user ) {
		if ( !UserPost::first(['user_id' => $user->id, 'post_id' => $id]) ) {
			$key = $g_user->decrypt($post->crypter);
			UserPost::insert([
				'user_id' => $user->id,
				'post_id' => $id,
				'crypter' => $user->encrypt($key),
			]);
		}

		return do_redirect('post', ['id' => $id, 'msg' => 'User invited']);
	}

	return do_redirect('index', ['msg' => 'Invalid user']);
}

include 'tpl/header.php';

?>
<p><a href="index.php">Home</a></p>

<h1><?= html($post->decrypted_title) ?></h1>

<blockquote>
	<?= nl2br(html($post->decrypted_body)) ?>
</blockquote>

<form method="post">
	<p>Username: <input required name="invite_user" /></p>
	<p><button>Invite user</button>
</form>

<pre><? print_r($post) ?></pre>

<?php

include 'tpl/footer.php';
