<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'profile_picture' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null' => true,
            ],
            'blood_group' => [
                'type' => 'ENUM',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'],
                'null' => true,
            ],
            'emergency_contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'emergency_contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],
            'emergency_contact_relation' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'two_factor_auth_enabled' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'two_factor_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'notification_preferences' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('user_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('user_profiles');
    }
}
