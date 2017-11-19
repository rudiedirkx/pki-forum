<?php

namespace App;

/**
 * @property int $post_id
 * @property string $crypter
 *
 * @property string $decrypted_title
 * @property string $decrypted_body
 * @property Post $post
 */
interface PostInterface {
	/** @return string */
	function get_decrypted_title();

	/** @return string */
	function get_decrypted_body();

	/** @return Post */
	function get_post();
}
