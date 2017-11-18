<!doctype html>
<html>

<head>
<title>PKI forum</title>
<style>
form {
	border: solid 1px #aaa;
	padding: 10px;
}
form > :first-child {
	margin-top: 0;
}
form > :last-child {
	margin-bottom: 0;
}
</style>
</head>

<body>

<? if ($g_user): ?>
	<p>Logged in as <code><?= html($g_user->username) ?></code>.</p>
<? endif ?>

<? if (@$_GET['msg']): ?>
	<p style="font-size: 120%; color: blue"><?= html($_GET['msg']) ?></p>
<? endif ?>
