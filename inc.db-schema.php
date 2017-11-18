<?php

$fk = function($tbl, $null, $delete = null) {
	return ['null' => $null, 'type' => 'int', 'references' => [$tbl, 'id', $delete]];
};

return [
	'version' => 4,
	'tables' => [
		'users' => [
			'id' => ['pk' => true],
			'username' => ['null' => false],
			'private_key',
			'public_key',
		],
		'posts' => [
			'id' => ['pk' => true],
			'title' => ['null' => false],
			'body',
		],
		'users_posts' => [
			'columns' => [
				'id' => ['pk' => true],
				'user_id' => $fk('users', false, 'cascade'),
				'post_id' => $fk('posts', false, 'cascade'),
				'crypter',
			],
			'indexes' => [
				'user_post' => [
					'columns' => ['user_id', 'post_id'],
					'unique' => true,
				],
			],
		],
	],
];
