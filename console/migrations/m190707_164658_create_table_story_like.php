<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190707_164658_create_table_story_like
 */
class m190707_164658_create_table_story_like extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%story_like}}', [
            'story_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'action' => $this->smallInteger()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-story_like', '{{%story_like}}', ['story_id', 'user_id']);

        $this->addForeignKey(
            'fk-story_like-story_id',
            '{{%story_like}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-story_like-user_id',
            '{{%story_like}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%story_like}}');
        $this->dropForeignKey('fk-story_like-story_id', '{{%story_like}}');
        $this->dropForeignKey('fk-story_like-user_id', '{{%story_like}}');
        $this->dropTable('{{%story_like}}');
    }

}
