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
