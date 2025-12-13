<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks temporarily
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // Truncate the users table to erase all existing accounts
        $this->db->table('users')->truncate();
        
        // Re-enable foreign key checks
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        $data = [
            [
                'name'       => 'Admin User',
                'email'      => 'admin@hospital.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'phone'      => '555-0001',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Dr. John Smith',
                'email'      => 'john.smith@hospital.com',
                'password'   => password_hash('doctor123', PASSWORD_DEFAULT),
                'role'       => 'doctor',
                'phone'      => '555-0101',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Nurse Mary Johnson',
                'email'      => 'mary.johnson@hospital.com',
                'password'   => password_hash('nurse123', PASSWORD_DEFAULT),
                'role'       => 'nurse',
                'phone'      => '555-0201',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Receptionist Sarah Lee',
                'email'      => 'sarah.lee@hospital.com',
                'password'   => password_hash('reception123', PASSWORD_DEFAULT),
                'role'       => 'receptionist',
                'phone'      => '555-0301',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Pharmacist Mike Brown',
                'email'      => 'mike.brown@hospital.com',
                'password'   => password_hash('pharma123', PASSWORD_DEFAULT),
                'role'       => 'pharmacist',
                'phone'      => '555-0401',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
