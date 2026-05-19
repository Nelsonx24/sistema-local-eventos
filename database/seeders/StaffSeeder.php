<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        Staff::create([
            'name' => 'Administrador',
            'email' => 'admin@grancanaveral.com',
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'Administrador',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Admin',
        ]);

        Staff::create([
            'name' => 'Vendedor Demo',
            'email' => 'vendedor@grancanaveral.com',
            'username' => 'vendedor',
            'password' => 'vendedor123',
            'role' => 'Vendedor',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Vendedor',
        ]);

        Staff::create([
            'name' => 'CM Demo',
            'email' => 'cm@grancanaveral.com',
            'username' => 'cm',
            'password' => 'cm123',
            'role' => 'CM',
            'status' => 'Active',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=CM',
        ]);
    }
}
