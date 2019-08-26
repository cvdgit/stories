<?php

use yii\db\Migration;

/**
 * Class m190816_062658_create_table_story_test_result
 */
class m190816_062658_create_table_story_test_result extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_test_result}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'answer_is_correct' => $this->tinyInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_test_result-question_id',
            $this->tableName,
            'question_id',
            '{{%story_test_question}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_story_test_result-user_id',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_story_test_result-story_id',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
