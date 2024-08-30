<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map_history}}`.
 */
class M240829123112CreateMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'story_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'mental_map_id' => $this->string(36)->notNull(),
            'image_fragment_id' => $this->string(36)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'content' => $this->text()->notNull(),
            'overall_similarity' => $this->tinyInteger()->notNull(),
            'text_hiding_percentage' => $this->tinyInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'PRIMARY KEY (`id`)',
        ]);

        $this->createIndex('{{%idx-mental_map_history-story_id}}', $this->tableName, 'story_id');
        $this->addForeignKey(
            '{{%fk-mental_map_history-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->createIndex('{{%idx-mental_map_history-mental_map_id}}', $this->tableName, 'mental_map_id');
        $this->addForeignKey(
            '{{%fk-mental_map_history-mental_map_id}}',
            $this->tableName,
            'mental_map_id',
            '{{%mental_map}}',
            'uuid',
            'CASCADE'
        );

        $this->createIndex('{{%idx-mental_map_history-user_id}}', $this->tableName, 'user_id');
        $this->addForeignKey(
            '{{%fk-mental_map_history-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex('{{%idx-mental_map_history-created_at}}', $this->tableName, 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-mental_map_history-created_at}}', $this->tableName);
        $this->dropForeignKey('{{%fk-mental_map_history-user_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_history-user_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-mental_map_history-mental_map_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_history-mental_map_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-mental_map_history-story_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_history-story_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
