<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190225_145235_create_table_story_feedback
 */
class m190225_145235_create_table_story_feedback extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%story_feedback}}', [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer()->notNull(),
            'assign_user_id' => $this->integer()->notNull(),
            'slide_number' => $this->integer()->notNull(),
            'text' => $this->string(255)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_storyfeedback_story_id',
            '{{%story_feedback}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_storyfeedback_user_id',
            '{{%story_feedback}}',
            'assign_user_id',
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
        echo "m190225_145235_create_table_story_feedback cannot be reverted.\n";
        $this->truncateTable('{{%story_feedback}} CASCADE');
        $this->dropForeignKey('fk_storyfeedback_story_id', '{{%story_feedback}}');
        $this->dropForeignKey('fk_storyfeedback_user_id', '{{%story_feedback}}');
        $this->dropTable('{{%story_feedback}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190225_145235_create_table_story_feedback cannot be reverted.\n";

        return false;
    }
    */
}
