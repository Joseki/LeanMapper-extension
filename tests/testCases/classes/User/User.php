<?php

namespace App\Tables;

use Joseki\LeanMapper\BaseEntity;
use Nette\Security\IIdentity;



/**
 * @property string $username
 * @property string $password
 * @property Role[] $roles m:hasMany
 */
class User extends BaseEntity implements IIdentity
{
	/**
	 * Returns the ID of user.
	 * @return mixed
	 */
	function getId()
	{
		$data = $this->row->getData();
		return $data['id'];
	}



	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	function getRoles()
	{
		$data = $this->row->getData();
		return $data['roles'];
	}
}
