<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%edu_required_story}}`.
 */
class M260702140928AddPriorityColumnToEduRequiredStoryTable extends Migration
{
    private $tableName = '{{%edu_required_story}}';
    private $columnName = 'priority';

    public function up(): void
    {
        $this->addColumn(
            $this->tableName,
            $this->columnName,
            $this->tinyInteger()->defaultValue(0),
        );
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
