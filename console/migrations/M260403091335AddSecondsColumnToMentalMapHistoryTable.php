<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M260403091335AddSecondsColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
    private $columnName = 'seconds';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
