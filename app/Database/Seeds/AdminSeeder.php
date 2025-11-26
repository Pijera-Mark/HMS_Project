<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'      => 'System Administrator',
            'email'     => 'admin@hms.com',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
            'role'      => 'admin',
            'status'    => 'active',
            'branch_id' => null,
            'phone'     => null,
            'avatar_url'=> null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('users')->insert($data);
        echo "Admin user created successfully!\n";
        echo "Email: admin@hms.com\n";
        echo "Password: admin123\n";
    }
}
