<!doctype html>
<html>

<head>
<title>PKI forum</title>
<style></style>
</head>

<body>

<? if (@$_GET['msg']): ?>
	<p style="font-size: 120%; color: blue"><?= html($_GET['msg']) ?></p>
<? endif ?>
