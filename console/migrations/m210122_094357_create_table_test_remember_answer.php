<?php

use yii\db\Migration;

/**
 * Class m210122_094357_create_table_test_remember_answer
 */
class m210122_094357_create_table_test_remember_answer extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%test_remember_answer}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'test_id' => $this->integer()->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'answer' => $this->string()->notNull(),
            'PRIMARY KEY(test_id, entity_id)',
        ], $this->tableOptions);

        $this->createIndex(
            '{{%idx-test_remember_answer-test_id}}',
            $this->tableName,
            'test_id'
        );

        $this->addForeignKey(
            '{{%fk-test_remember_answer-test_id}}',
            $this->tableName,
            'test_id',
            '{{%story_test}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-test_remember_answer-entity_id}}',
            $this->tableName,
            'entity_id'
        );

        $this->addForeignKey(
            '{{%fk-test_remember_answer-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
