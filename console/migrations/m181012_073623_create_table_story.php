<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m181012_073623_create_table_story
 */
class m181012_073623_create_table_story extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%story}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'alias' => $this->string(255)->notNull()->unique(),
            'body' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull()
        ], $tableOptions);
        $this->addForeignKey(
            'fk_story_user_id',
            '{{%story}}',
            'user_id',
            '{{%user}}',
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
        $this->truncateTable('{{%story}} CASCADE');
        $this->dropForeignKey('fk_story_user_id', '{{%story}}');
        $this->dropTable('{{%story}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181012_073623_create_table_story cannot be reverted.\n";

        return false;
    }
    */
}
