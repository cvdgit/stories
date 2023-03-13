<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_question_history}}`.
 */
class m200514_084203_create_user_question_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_question_history}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'question_topic_id' => $this->integer()->notNull(),
            'question_topic_name' => $this->string()->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'entity_name' => $this->string(512)->notNull(),
            'relation_id' => $this->integer()->notNull(),
            'relation_name' => $this->string()->notNull(),
            'correct_answer' => $this->tinyInteger()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'fk-user_question_history-user_id',
            '{{%user_question_history}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-user_question_history-slide_id',
            '{{%user_question_history}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_question_history}}');
    }
}
