<?php

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

/**
 * @property int id
 */
class Model extends db_generic_model {

	/** @return string */
	static public function _encrypt( $key, $data ) {
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = openssl_random_pseudo_bytes($iv_size);
		return base64_encode($iv . openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
	}

	/** @return string */
	static public function _decrypt( $key, $data ) {
		$data = base64_decode($data);
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($data, 0, $iv_size);
		$data = substr($data, $iv_size);
		return rtrim(openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv), "\0");
	}

	/** @return string[] */
	static public function _pkey( $passphrase ) {
		$pkey = openssl_pkey_new(['digest_alg' => 'sha512', 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
		openssl_pkey_export($pkey, $privkey, $passphrase);
		$details = openssl_pkey_get_details($pkey);
		$pubkey = $details['key'];

		return [$privkey, $pubkey];
	}

}

/**
 * @property string $username
 * @property string $private_key
 * @property string $public_key
 *
 * @property resource $pkey
 * @property GroupUser[] $groups
 * @property int[] $gids
 * @property PostInterface[] $all_posts
 */
class User extends Model {

	static public $_table = 'users';

	protected function get_groups() {
		/** @var GroupUser[] $groups */
		$groups = GroupUser::all(['user_id' => $this->id]);
		foreach ( $groups as $group ) {
			$password = $this->decrypt($group->passphrase);
			$group->group->pkey = openssl_pkey_get_private($group->group->private_key, $password);
		}
		return $groups;
	}

	protected function get_gids() {
		return array_map(function(GroupUser $group) {
			return $group->group_id;
		}, $this->groups);
	}

	protected function get_all_posts() {
		$gids = $this->gids;
		$userPosts = UserPost::all(['user_id' => $this->id]);
		$groupPosts = GroupPost::all(['group_id' => $gids]);

		$posts = [];
		foreach ( $groupPosts as $post ) {
			$posts[$post->post_id] = $post;
		}
		foreach ( $userPosts as $post ) {
			$posts[$post->post_id] = $post;
		}
		return $posts;
	}

	public function encrypt( $data ) {
		if ( !openssl_public_encrypt($data, $output, $this->public_key) ) {
			throw new Exception(__METHOD__);
		}

		return base64_encode($output);
	}

	public function decrypt( $data ) {
		if ( !openssl_private_decrypt(base64_decode($data), $output, $this->pkey) ) {
			throw new Exception(__METHOD__);
		}

		return $output;
	}

	public function __toString() {
		return $this->username;
	}

}

/**
 * @property string $title
 * @property string $body
 *
 * @property UserPost[] $user_posts
 */
class Post extends Model {

	static public $_table = 'posts';

	protected function get_user_posts() {
		return UserPost::all(['post_id' => $this->id]);
	}

	/** @return PostInterface */
	static public function get( User $user, $id ) {
		return
			UserPost::first(['user_id' => $user->id, 'post_id' => $id]) ?:
			GroupPost::first(['group_id' => $user->gids, 'post_id' => $id]);
	}

}

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

/**
 * @property string $name
 * @property string $private_key
 * @property string $public_key
 *
 * @property resource $pkey
 */
class Group extends Model {

	static public $_table = 'groups';

	public function __toString() {
		return $this->name;
	}

	public function encrypt( $data ) {
		if ( !openssl_public_encrypt($data, $output, $this->public_key) ) {
			throw new Exception(__METHOD__);
		}

		return base64_encode($output);
	}

	public function decrypt( $data ) {
		if ( !openssl_private_decrypt(base64_decode($data), $output, $this->pkey) ) {
			throw new Exception(__METHOD__);
		}

		return $output;
	}

}

/**
 * @property int $group_id
 * @property int $user_id
 * @property string $passphrase
 *
 * @property string $decrypted_name
 * @property Group $group
 * @property User $user
 */
class GroupUser extends Model {

	static public $_table = 'groups_users';

	protected function get_group() {
		return Group::find($this->group_id);
	}

	protected function get_user() {
		return User::find($this->user_id);
	}

	protected function get_decrypted_name() {
		return $this->group->decrypt($this->group->name);
	}

	public function __toString() {
		return $this->decrypted_name;
	}

}

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

	protected function get_group() {
		return Group::find($this->group_id);
	}

	/*protected*/ function get_post() {
		return Post::find($this->post_id);
	}

	/*protected*/ function get_decrypted_title() {
		return $this->decrypt($this->group, $this->post->title);
	}

	/*protected*/ function get_decrypted_body() {
		return $this->decrypt($this->group, $this->post->body);
	}

	public function decrypt( Group $group, $data ) {
		$key = $group->decrypt($this->crypter);
		return self::_decrypt($key, $data);
	}

}
