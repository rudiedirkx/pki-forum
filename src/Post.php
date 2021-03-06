<?php

namespace App;

/**
 * @property string $title
 * @property string $body
 */
class Post extends Model {

	static public $_table = 'posts';

	/** @return PostInterface */
	static function get( User $user, $id ) {
		return
			UserPost::first(['user_id' => $user->id, 'post_id' => $id]) ?:
			GroupPost::first(['group_id' => $user->gids, 'post_id' => $id]);
	}

}
