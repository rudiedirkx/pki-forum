<?php

namespace App;

/**
 * @property int $group_id
 * @property int $post_id
 * @property string $crypter
 *
 * @property Group $group
 * @property Post $post
 */
class GroupPost extends Model implements PostInterface {

	static public $_table = 'groups_posts';

	function get_group() {
		return Group::find($this->group_id);
	}

	function get_post() {
		return Post::find($this->post_id);
	}

	function get_decrypted_title() {
		return $this->decrypt($this->group, $this->post->title);
	}

	function get_decrypted_body() {
		return $this->decrypt($this->group, $this->post->body);
	}

	function decrypt( Group $group, $data ) {
		$key = $group->decrypt($this->crypter);
		return self::_decrypt($key, $data);
	}

}
