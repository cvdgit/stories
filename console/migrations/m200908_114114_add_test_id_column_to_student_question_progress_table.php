<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%student_question_progress}}`.
 */
class m200908_114114_add_test_id_column_to_student_question_progress_table extends Migration
{

    private $tableName = '{{%student_question_progress}}';
    private $tableColumnName = 'test_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->integer()->notNull());

        $this->dropForeignKey('fk-student_question_progress-student_id', $this->tableName);
        $this->dropPrimaryKey('pk-student_question_progress', $this->tableName);

        $command = \Yii::$app->db->createCommand('UPDATE student_question_progress t
                                                     SET t.test_id = (SELECT t2.id FROM story_test t2 WHERE t2.question_list_id = t.question_id)');
        $command->execute();

        $this->addPrimaryKey('pk-student_question_progress', $this->tableName, ['student_id', 'test_id']);
        $this->addForeignKey(
            'fk-student_question_progress-student_id',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
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
        $this->dropColumn($this->tableName, $this->tableColumnName);
        $this->dropForeignKey('fk-student_question_progress-student_id', $this->tableName);
        $this->dropPrimaryKey('pk-student_question_progress', $this->tableName);
        $this->addPrimaryKey('pk-student_question_progress', $this->tableName, ['student_id', 'question_id']);
        $this->addForeignKey(
            'fk-student_question_progress-student_id',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
