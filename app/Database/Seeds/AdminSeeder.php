<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'      => 'Hospital Administrator',
            'email'     => 'admin@hospital.com',
            'password'  => password_hash('Hospital@2024', PASSWORD_DEFAULT),
            'role'      => 'admin',
            'status'    => 'active',
            'branch_id' => null,
            'phone'     => null,
            'avatar_url'=> null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Remove any existing admin accounts first
        $this->db->table('users')->where('role', 'admin')->delete();

        // Insert the new admin
        $this->db->table('users')->insert($data);
        echo "Admin user created successfully!\n";
        echo "Email: admin@hospital.com\n";
        echo "Password: Hospital@2024\n";
    }
}
