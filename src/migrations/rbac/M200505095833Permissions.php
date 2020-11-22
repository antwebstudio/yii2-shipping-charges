<?php

namespace ant\shipping\migrations\rbac;

use yii\db\Schema;
use ant\rbac\Migration;
use ant\rbac\Role;

class M200505095833Permissions extends Migration
{
	protected $permissions;
	
	public function init() {
		$this->permissions = [
			\ant\shipping\backend\controllers\ShippingRuleController::class => [
				'index' => ['View shipping rules', [Role::ROLE_ADMIN]],
				'create' => ['View shipping rules', [Role::ROLE_ADMIN]],
				'update' => ['View shipping rules', [Role::ROLE_ADMIN]],
				'delete' => ['View shipping rules', [Role::ROLE_ADMIN]],
				'manage' => ['View shipping rules', [Role::ROLE_ADMIN]],
				'basic' => ['View shipping rules', [Role::ROLE_ADMIN]],
			],
		];
		
		parent::init();
	}
	
	public function up()
    {
		$this->addAllPermissions($this->permissions);
    }

    public function down()
    {
		$this->removeAllPermissions($this->permissions);
    }
}
