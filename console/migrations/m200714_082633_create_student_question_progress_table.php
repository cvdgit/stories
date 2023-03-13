<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_question_progress}}`.
 */
class m200714_082633_create_student_question_progress_table extends Migration
{

    protected $tableName = '{{%student_question_progress}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'student_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'progress' => $this->tinyInteger()->defaultValue(0),
        ]);
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
