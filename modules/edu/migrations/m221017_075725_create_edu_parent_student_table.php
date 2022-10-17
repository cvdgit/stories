<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_parent_student}}`.
 */
class m221017_075725_create_edu_parent_student_table extends Migration
{
    private $tableName = '{{%edu_parent_student}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'parent_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'PRIMARY KEY(parent_id, student_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_parent_student-parent_id}}',
            $this->tableName,
            'parent_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-edu_parent_student-student_id}}',
            $this->tableName,
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
        $this->dropForeignKey('{{%fk-edu_parent_student-student_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_parent_student-parent_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
