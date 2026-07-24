<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class M260724075014AddPayloadColumnToStoryTestQuestionTable extends Migration
{
    private $tableName = '{{%story_test_question}}';
    private $columnName = 'payload';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->json()->null());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
