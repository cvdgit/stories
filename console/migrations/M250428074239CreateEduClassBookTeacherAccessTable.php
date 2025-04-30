<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class_book_teacher_access}}`.
 */
class M250428074239CreateEduClassBookTeacherAccessTable extends Migration
{
    private $tableName = '{{%edu_class_book_teacher_access}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'class_book_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'access_type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'PRIMARY KEY (class_book_id, teacher_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_class_book_teacher_access-class_book_id}}',
            $this->tableName,
            'class_book_id',
            '{{%edu_class_book}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-edu_class_book_teacher_access-teacher_id}}', $this->tableName, 'teacher_id');
        $this->addForeignKey(
            '{{%fk-edu_class_book_teacher_access-teacher_id}}',
            $this->tableName,
            'teacher_id',
            '{{%user}}',
            'id',
            'CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-edu_class_book_teacher_access-teacher_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_class_book_teacher_access-teacher_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_class_book_teacher_access-class_book_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
