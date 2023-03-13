<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190710_092014_create_table_story_favorites
 */
class m190710_092014_create_table_story_favorites extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%story_favorites}}', [
            'user_id' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-story_favorites', '{{%story_favorites}}', ['user_id', 'story_id']);

        $this->addForeignKey(
            'fk-story_favorites-user_id',
            '{{%story_favorites}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-story_favorites-story_id',
            '{{%story_favorites}}',
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
        $this->truncateTable('{{%story_favorites}}');
        $this->dropForeignKey('fk-story_favorites-story_id', '{{%story_favorites}}');
        $this->dropForeignKey('fk-story_favorites-user_id', '{{%story_favorites}}');
        $this->dropTable('{{%story_favorites}}');
    }

}
