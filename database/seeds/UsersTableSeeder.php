<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create admin user
        $admin = new User();
        $admin->fill([
            'name' => 'Admin',
            'email' => 'admin@szrd.com',
            'password' => Crypt::encrypt('pwd1234'),
        ])->save();
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole->id);

        $user = new User();
        $user->fill([
            'name' => '来料检测员',
            'email' => 'iqc@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'iqc')->first();
        $user->roles()->attach($role->id);

        $user = new User();
        $user->fill([
            'name' => '原料仓管',
            'email' => 'user002@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'material_warehouse_keeper')->first();
        $user->roles()->attach($role->id);

        $user = new User();
        $user->fill([
            'name' => '成品仓管',
            'email' => 'user003@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'finished_warehouse_keeper')->first();
        $user->roles()->attach($role->id);

        $user = new User();
        $user->fill([
            'name' => '员工',
            'email' => 'user001@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'stuff')->first();
        $user->roles()->attach($role->id);

        $user = new User();
        $user->fill([
            'name' => 'boss',
            'email' => 'user999@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'boss')->first();
        $user->roles()->attach($role->id);

        $user = new User();
        $user->fill([
            'name' => '管代',
            'email' => 'user008@szrd.com',
            'password' => Crypt::encrypt('123456'),
        ])->save();
        $role = Role::where('name', 'management_representative')->first();
        $user->roles()->attach($role->id);
    }
}
