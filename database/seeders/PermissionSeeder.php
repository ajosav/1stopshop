<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = config('permission.default_permissions');
        $roles = config('permission.default_roles');
        foreach($permissions as $permission) {
            if(!isPermissionExist($permission)) {
                Permission::create(['name' => $permission]);
            }
        }
        foreach($roles as $role) {
            $roles = Role::create(['name' => $role]);
            // if($role = 'admin') {
            //     $roles->giverPermissionTo(['admin_user', 'admin_user_2']);
            // }
        }
    }
}
