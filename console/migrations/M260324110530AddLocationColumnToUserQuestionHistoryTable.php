<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_question_history}}`.
 */
class M260324110530AddLocationColumnToUserQuestionHistoryTable extends Migration
{
    private $tableName = '{{%user_question_history}}';
    private $columnName = 'location';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(10));
        $this->createIndex('{{%idx-user_question_history-location}}', $this->tableName, 'location');
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-user_question_history-location}}', $this->tableName);
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
