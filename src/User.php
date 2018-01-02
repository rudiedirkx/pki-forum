<?php

namespace App;

use Exception;

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

	function get_groups() {
		/** @var GroupUser[] $groups */
		$groups = GroupUser::all(['user_id' => $this->id]);
		foreach ( $groups as $group ) {
			$password = $this->decrypt($group->passphrase);
			$group->group->pkey = openssl_pkey_get_private($group->group->private_key, $password);
		}
		return $groups;
	}

	function get_gids() {
		return array_map(function(GroupUser $group) {
			return $group->group_id;
		}, $this->groups);
	}

	function get_all_posts() {
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

	function encrypt( $data ) {
		if ( !openssl_public_encrypt($data, $output, $this->public_key) ) {
			throw new Exception(__METHOD__);
		}

		return base64_encode($output);
	}

	function decrypt( $data ) {
		if ( !openssl_private_decrypt(base64_decode($data), $output, $this->pkey) ) {
			throw new Exception(__METHOD__);
		}

		return $output;
	}

	function __toString() {
		return $this->username;
	}

}
