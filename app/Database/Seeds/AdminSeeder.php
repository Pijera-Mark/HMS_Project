<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'      => 'Hospital Administrator',
            'email'     => 'administrator@hms.local',
            'password'  => password_hash('hms@dm1n#2024', PASSWORD_DEFAULT),
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
        echo "Email: administrator@hms.local\n";
        echo "Password: hms@dm1n#2024\n";
    }
}
