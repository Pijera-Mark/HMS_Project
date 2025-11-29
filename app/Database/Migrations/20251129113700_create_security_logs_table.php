<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSecurityLogsTable extends Migration
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
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'request_uri' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'request_method' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'context' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'severity' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default' => 'medium',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'resolved', 'ignored'],
                'default' => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['event_type']);
        $this->forge->addKey(['user_id']);
        $this->forge->addKey(['ip_address']);
        $this->forge->addKey(['severity']);
        $this->forge->addKey(['created_at']);
        
        $this->forge->createTable('security_logs');
    }

    public function down()
    {
        $this->forge->dropTable('security_logs');
    }
}
