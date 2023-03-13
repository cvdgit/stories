<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190605_071736_create_table_user_story_history
 */
class m190605_071736_create_table_user_story_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%user_story_history}}', [
            'user_id' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-user_story_history', '{{%user_story_history}}', ['user_id', 'story_id']);

        $this->addForeignKey(
            'fk-user_story_history-user_id',
            '{{%user_story_history}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-user_story_history-story_id',
            '{{%user_story_history}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%user_story_history}}');
        $this->dropForeignKey('fk-user_story_history-user_id', '{{%user_story_history}}');
        $this->dropForeignKey('fk-user_story_history-story_id', '{{%user_story_history}}');
        $this->dropTable('{{%user_story_history}}');
    }

}
