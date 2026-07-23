<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%column_abort_log}}`.
 */
class M260723063856CreateColumnAbortLogTable extends Migration
{
    private $tableName = '{{%column_abort_log}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'payload' => $this->json()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%idx-column_abort_log-question_id}}', $this->tableName, 'question_id');
        $this->addForeignKey(
            '{{%fk-column_abort_log-question_id}}',
            $this->tableName,
            'question_id',
            '{{%story_test_question}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-column_abort_log-student_id}}', $this->tableName, 'student_id');
        $this->addForeignKey(
            '{{%fk-column_abort_log-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-column_abort_log-student_id}}', $this->tableName);
        $this->dropIndex('{{%idx-column_abort_log-student_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-column_abort_log-question_id}}', $this->tableName);
        $this->dropIndex('{{%idx-column_abort_log-question_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
