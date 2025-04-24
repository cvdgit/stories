<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M250424082438AddThresholdColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
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
