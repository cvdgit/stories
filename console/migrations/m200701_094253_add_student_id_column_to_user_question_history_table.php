<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_question_history}}`.
 */
class m200701_094253_add_student_id_column_to_user_question_history_table extends Migration
{

    protected $tableName = '{{%user_question_history}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'student_id', $this->integer()->notNull()->after('id'));
        $this->update($this->tableName, ['student_id' => 4]);
        $this->addForeignKey(
            'fk-user_question_history-student_id',
            '{{%user_question_history}}',
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_question_history-student_id', $this->tableName);
        $this->dropColumn($this->tableName, 'student_id');
    }

}
