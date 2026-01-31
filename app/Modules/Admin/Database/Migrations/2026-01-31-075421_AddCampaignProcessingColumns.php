<?php

namespace App\Modules\Admin\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCampaignProcessingColumns extends Migration
{
    public function up()
    {
        // Add columns to the 'campaigns' table
        $this->forge->addColumn('campaigns', [
            'status'             => ['type' => 'ENUM', 'constraint' => ['draft', 'pending', 'sending', 'completed', 'paused', 'retry_mode'], 'default' => 'draft'],
            'last_processed_id'  => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'sent_count'         => ['type' => 'INT', 'default' => 0],
            'error_count'        => ['type' => 'INT', 'default' => 0],
            'total_recipients'   => ['type' => 'INT', 'default' => 0],
            'stop_at_count'      => ['type' => 'INT', 'default' => 0],
            'max_user_id'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'quota_hit_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        // Create campaign_logs table
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'campaign_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['sent', 'failed']],
            'error_message'  => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        // Add composite index for fast retry lookups
        $this->forge->addKey(['campaign_id', 'status']);
        $this->forge->createTable('campaign_logs');

        // Add indices to 'campaigns' table (Raw SQL for existing table modification)
        $this->db->query("ALTER TABLE `campaigns` ADD INDEX `idx_quota_hit_at` (`quota_hit_at`)");
        $this->db->query("ALTER TABLE `campaigns` ADD INDEX `idx_created_at` (`created_at`)");
    }

    public function down()
    {
        $this->forge->dropTable('campaign_logs');
        // Drop indices first to be safe, though dropColumn might handle it
        $this->db->query("ALTER TABLE `campaigns` DROP INDEX `idx_quota_hit_at`");
        $this->db->query("ALTER TABLE `campaigns` DROP INDEX `idx_created_at`");
        $this->forge->dropColumn('campaigns', ['status', 'last_processed_id', 'sent_count', 'error_count', 'total_recipients', 'stop_at_count', 'max_user_id', 'quota_hit_at']);
    }
}
