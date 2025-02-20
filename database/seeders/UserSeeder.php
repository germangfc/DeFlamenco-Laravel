<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisoAdmin = Permission::create(['name' => 'admin']);
        $permisoEmpresa = Permission::create(['name' => 'empresa']);
        $permisoCliente = Permission::create(['name' => 'cliente']);

        $roladmin = Role::create(['name' => 'admin']);
        $rolEmpresa = Role::create(['name' => 'empresa']);
        $rolCliente = Role::create(['name' => 'cliente']);

        $roladmin->givePermissionTo([$permisoAdmin]);
        $rolEmpresa->givePermissionTo([$permisoEmpresa]);
        $rolCliente->givePermissionTo([$permisoCliente]);


        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        $adminUser->assignRole('admin');
        $adminUser->syncPermissions([$permisoAdmin ]);

    }
}
