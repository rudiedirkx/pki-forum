<?php

require 'inc.bootstrap.php';

$user = check_login();

if ( isset($_POST['title'], $_POST['body']) ) {
	$key = get_rand();
	$title = Post::encrypt($key, trim($_POST['title']));
	$body = Post::encrypt($key, trim($_POST['body']));

	$db->begin();

	$id = Post::insert([
		'title' => $title,
		'body' => $body,
	]);

	UserPost::insert([
		'user_id' => $user->id,
		'post_id' => $id,
		'crypter' => $user->encrypt($key),
	]);

	$db->commit();

	return do_redirect('index');
}

include 'tpl.header.php';

$posts = UserPost::all(['user_id' => $user->id]);

?>
<h1>Home</h1>

<ul>
	<? foreach ($posts as $post): ?>
		<li><a href="post.php?id=<?= html($post->post_id) ?>"><?= html($post->decrypted_title) ?></a></li>
	<? endforeach ?>
</ul>

<form method="post">
	<p>Title: <input name="title" /></p>
	<p>Body:<br><textarea name="body"></textarea></p>
	<p><button>Save</button></p>
</form>

<pre><? print_r($user) ?></pre>

<?php

include 'tpl.footer.php';
