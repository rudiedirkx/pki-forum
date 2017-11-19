<?php

namespace App;

/**
 * @property int $user_id
 * @property int $post_id
 * @property string $crypter
 *
 * @property string $decrypted_title
 * @property string $decrypted_body
 * @property User $user
 * @property Post $post
 */
class UserPost extends Model implements PostInterface {

	static public $_table = 'users_posts';

	/*protected*/ function get_post() {
	return Post::find($this->post_id);
}

	protected function get_user() {
		return User::find($this->user_id);
	}

	/*protected*/ function get_decrypted_title() {
	return $this->decrypt($this->user, $this->post->title);
}

	/*protected*/ function get_decrypted_body() {
	return $this->decrypt($this->user, $this->post->body);
}

	public function decrypt( User $user, $data ) {
		$key = $user->decrypt($this->crypter);
		return self::_decrypt($key, $data);
	}

}
