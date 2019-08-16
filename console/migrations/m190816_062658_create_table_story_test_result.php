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
            'story_test_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'correct_answer' => $this->smallInteger()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_test_result-story_test_id',
            $this->tableName,
            'story_test_id',
            '{{%story_test}}',
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
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
