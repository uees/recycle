<?php

use Illuminate\Database\Seeder;


class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app('db')->table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => '管理员'
            ],
            [
                'name' => 'iqc',
                'display_name' => '来料检测员'
            ],
            [
                'name' => 'finished_warehouse_keeper',
                'display_name' => '成品仓管'
            ],
            [
                'name' => 'material_warehouse_keeper',
                'display_name' => '原料仓管'
            ],
            [
                'name' => 'stuff',
                'display_name' => '员工'
            ],
            [
                'name' => 'boss',
                'display_name' => '老板'
            ],
            [
                'name' => 'management_representative',
                'display_name' => '管理者代表'
            ],
        ]);
    }
}
