<?php

namespace App;

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

	function get_group() {
		return Group::find($this->group_id);
	}

	function get_user() {
		return User::find($this->user_id);
	}

	function get_decrypted_name() {
		return $this->group->decrypt($this->group->name);
	}

	function __toString() {
		return $this->decrypted_name;
	}

}
