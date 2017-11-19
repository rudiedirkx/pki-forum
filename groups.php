<?php

require 'inc.bootstrap.php';

$g_user = check_login();

$groups = $g_user->groups;

if ( isset($_POST['new_group']) ) {
	$passphrase = get_rand();

	list($privkey, $pubkey) = Model::_pkey($passphrase);

	$id = Group::insert([
		'name' => '',
		'private_key' => $privkey,
		'public_key' => $pubkey,
	]);
	$group = Group::find($id);

	$id = GroupUser::insert([
		'group_id' => $group->id,
		'user_id' => $g_user->id,
		'passphrase' => $g_user->encrypt($passphrase),
	]);
	$mship = GroupUser::find($id);

	$group->update([
		'name' => $mship->group->encrypt($_POST['new_group']),
	]);

	return do_redirect('groups');
}

if ( isset($_POST['invite_group'], $_POST['invite_user']) ) {
	/** @var GroupUser $group */
	$group = $groups[ $_POST['invite_group'] ];
	$user = get_user($_POST['invite_user']);
	if ( !$group || !$user ) {
		return do_redirect('groups', ['msg' => 'Invalid']);
	}

	$passphrase = $g_user->decrypt($group->passphrase);

	GroupUser::insert([
		'group_id' => $group->group_id,
		'user_id' => $user->id,
		'passphrase' => $user->encrypt($passphrase),
	]);

	return do_redirect('groups', ['msg' => 'User added']);
}

include 'tpl.header.php';

?>
<p><a href="index.php">Home</a></p>

<h1>Groups</h1>

<ul>
	<? foreach ($groups as $group): ?>
		<li><?= html($group) ?></li>
	<? endforeach ?>
</ul>

<form method="post" autocomplete="off">
	<p>Name: <input required name="new_group" /></p>
	<p><button>Create group</button></p>
</form>

<form method="post">
	<p>Group: <select required name="invite_group"><?= html_options($groups) ?></select></p>
	<p>Username: <input required name="invite_user" /></p>
	<p><button>Invite user</button>
</form>

<pre><? print_r($groups) ?></pre>

<?php

include 'tpl.footer.php';
