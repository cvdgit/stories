<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_recent_story}}`.
 */
class m211029_113921_create_user_recent_story_table extends Migration
{

    private $tableName = '{{%user_recent_story}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'user_id' => $this->integer(),
            'story_id' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, story_id)',
        ]);

        $this->createIndex(
            '{{%idx-user_recent_story-story_id}}',
            $this->tableName,
            'story_id'
        );
        $this->addForeignKey(
            '{{%fk-user_recent_story-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-user_recent_story-user_id}}',
            $this->tableName,
            'user_id'
        );
        $this->addForeignKey(
            '{{%fk-user_recent_story-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-user_recent_story-story_id}}',
            $this->tableName
        );
        $this->dropIndex(
            '{{%idx-user_recent_story-story_id}}',
            $this->tableName
        );
        $this->dropForeignKey(
            '{{%fk-user_recent_story-user_id}}',
            $this->tableName
        );
        $this->dropIndex(
            '{{%idx-user_recent_story-user_id}}',
            $this->tableName
        );
        $this->dropTable($this->tableName);
    }
}
