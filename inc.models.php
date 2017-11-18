<?php

class User extends db_generic_model {

	static public $_table = 'users';

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

class Post extends db_generic_model {

	static public $_table = 'posts';

	static public function encrypt( $key, $data ) {
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = openssl_random_pseudo_bytes($iv_size);
		return base64_encode($iv . openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
	}

	static public function decrypt( $key, $data ) {
		$data = base64_decode($data);
		$iv_size = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($data, 0, $iv_size);
		$data = substr($data, $iv_size);
		return rtrim(openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv), "\0");
	}

}

class UserPost extends db_generic_model {

	static public $_table = 'users_posts';

	protected function get_post() {
		return Post::find($this->post_id);
	}

	protected function get_user() {
		return User::find($this->user_id);
	}

	protected function get_decrypted_title() {
		return $this->decrypt($this->user, $this->post->title);
	}

	protected function get_decrypted_body() {
		return $this->decrypt($this->user, $this->post->body);
	}

	public function encrypt( User $user, $data ) {
	}

	public function decrypt( User $user, $data ) {
		$key = $user->decrypt($this->crypter);
		return Post::decrypt($key, $data);
	}

}

class Group extends db_generic_model {

	static public $_table = 'groups';

}

class GroupUser extends db_generic_model {

	static public $_table = 'groups_users';

	public function encrypt( $data ) {
	}

	public function decrypt( $data ) {
	}

}
