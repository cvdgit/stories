<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_question_answer}}`.
 */
class m200629_064414_create_user_question_answer_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_question_answer}}', [
            'id' => $this->primaryKey(),
            'question_history_id' => $this->integer()->notNull(),
            'answer_entity_id' => $this->integer()->notNull(),
            'answer_entity_name' => $this->string(512)->notNull(),
        ]);
        $this->addForeignKey(
            'fk-user_question_answer-question_history_id',
            '{{%user_question_answer}}',
            'question_history_id',
            '{{%user_question_history}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_question_answer}}');
    }

}
