<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%retelling_history}}`.
 */
class M260619125340AddThresholdColumnToRetellingHistoryTable extends Migration
{
    private $tableName = '{{%retelling_history}}';
    private $columnName = 'threshold';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->null());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
