<?php

namespace julio101290\boilerplateinventory\Database\Seeds;

use CodeIgniter\Config\Services;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

/**
 * Class BoilerplateSeeder.
 */
class BoilerplateInventory extends Seeder {

    /**
     * @var Authorize
     */
    protected $authorize;

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var Users
     */
    protected $users;

    public function __construct() {
        $this->authorize = Services::authorization();
        $this->db = \Config\Database::connect();
        $this->users = new UserModel();
    }

    public function run() {


        // Permission
        $this->authorize->createPermission('inventory-permission', 'Permission to view inventory list');
        
        $this->authorize->createPermission('saldos-permission', 'Permission to view inventory balance');

        $this->authorize->createPermission('saldos-permission', 'Permission to view inventory balance');

        $this->authorize->createPermission('saldos-permission', 'Permission to view inventory balance');

        $this->authorize->createPermission('saldos-permission', 'Permission to view inventory balance');

        $this->authorize->createPermission('saldos-permission', 'Permission to view inventory balance');

        // Assign Permission to user
        $this->authorize->addPermissionToUser('inventory-permission', 1);
        $this->authorize->addPermissionToUser('saldos-permission', 1);

    }
    
    
    

    public function down() {
        //
    }
}
