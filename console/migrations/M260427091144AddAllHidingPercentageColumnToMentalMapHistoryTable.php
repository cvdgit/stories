<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M260427091144AddAllHidingPercentageColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
    private $columnName = 'all_hiding_percentage';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
