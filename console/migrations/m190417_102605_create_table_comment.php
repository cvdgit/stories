<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190417_102605_create_table_comment
 */
class m190417_102605_create_table_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'body' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey(
            'fk_comment_user_id',
            '{{%comment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_comment_story_id',
            '{{%comment}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190417_102605_create_table_comment cannot be reverted.\n";
        $this->truncateTable('{{%comment}} CASCADE');
        $this->dropForeignKey('fk_comment_user_id', '{{%comment}}');
        $this->dropForeignKey('fk_comment_story_id', '{{%comment}}');
        $this->dropTable('{{%comment}}');
        return false;
    }

}
