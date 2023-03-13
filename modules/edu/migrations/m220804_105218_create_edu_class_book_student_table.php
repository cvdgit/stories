<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class_book_student}}`.
 */
class m220804_105218_create_edu_class_book_student_table extends Migration
{

    private $tableName = '{{%edu_class_book_student}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'class_book_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'PRIMARY KEY(class_book_id, student_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_class_book_student-class_book_id}}',
            $this->tableName,
            'class_book_id',
            '{{%edu_class_book}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-edu_class_book_student-student_id}}',
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
        $this->dropForeignKey('{{%fk-edu_class_book_student-student_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_class_book_student-class_book_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
