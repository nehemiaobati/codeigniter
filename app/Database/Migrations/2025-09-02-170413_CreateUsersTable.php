<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
                'null' => false,
            ],
            'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'reset_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'reset_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reset_expires' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'activate_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status_message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'force_pass_reset' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
