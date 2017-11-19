<?php

use App\GroupPost;
use App\Model;
use App\Post;
use App\UserPost;

require 'inc.bootstrap.php';

$g_user = check_login();

$groups = $g_user->groups;

if ( isset($_POST['post_title'], $_POST['post_body']) ) {
	$key = get_rand();
	$title = Model::_encrypt($key, trim($_POST['post_title']));
	$body = Model::_encrypt($key, trim($_POST['post_body']));

	$db->begin();

	$id = Post::insert([
		'title' => $title,
		'body' => $body,
	]);

	UserPost::insert([
		'user_id' => $g_user->id,
		'post_id' => $id,
		'crypter' => $g_user->encrypt($key),
	]);

	foreach ( (array) @$_POST['post_groups'] as $gid ) {
		$group = $groups[$gid];

		GroupPost::insert([
			'group_id' => $group->group_id,
			'post_id' => $id,
			'crypter' => $group->group->encrypt($key),
		]);
	}

	$db->commit();

	return do_redirect('index');
}

include 'tpl/header.php';

$posts = $g_user->all_posts;

?>
<p>
	<a href="logout.php">Log out</a>
	<a href="groups.php">Groups</a>
</p>

<h1>Home</h1>

<ul>
	<? foreach ($posts as $post): ?>
		<li><a href="post.php?id=<?= html($post->post_id) ?>"><?= html($post->decrypted_title) ?></a></li>
	<? endforeach ?>
</ul>

<form method="post" autocomplete="off">
	<p>Title: <input required name="post_title" /></p>
	<p>Body:<br><textarea required name="post_body"></textarea></p>
	<p>Groups: <select multiple name="post_groups"><?= html_options($groups) ?></select></p>
	<p><button>Create post</button></p>
</form>

<pre><? print_r($g_user) ?></pre>

<?php

include 'tpl/footer.php';
