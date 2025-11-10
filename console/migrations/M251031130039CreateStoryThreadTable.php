<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_thread}}`.
 */
class M251031130039CreateStoryThreadTable extends Migration
{
    private $tableName = '{{%story_thread}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'title' => $this->string(255)->notNull(),
            'text' => $this->text()->null(),
            'payload' => $this->json()->null(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'story_id' => $this->integer()->null(),
            'PRIMARY KEY(id)',
        ]);

        $this->createIndex('{{%idx-story_thread-user_id}}', $this->tableName, 'user_id');
        $this->addForeignKey(
            '{{%fk-story_thread-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT'
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-story_thread-user_id}}', $this->tableName);
        $this->dropIndex('{{%idx-story_thread-user_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
