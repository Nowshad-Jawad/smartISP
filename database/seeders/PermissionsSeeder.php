<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Manager' => [
                'Manager List',
                'Manager Add',
                'Manager Edit',
                'Manager Delete',
            ],

            'Roles' => [
                'Role List',
                'Role Add',
                'Role Edit',
                'Role Delete',
            ]
        ];
        
        foreach ($permissions as $k => $permission) {
            foreach ($permission as $item) {
                Permission::create([
                    'name' => $item,
                    'group_name' => $k,
                ]);
            }
        }
    }
}
