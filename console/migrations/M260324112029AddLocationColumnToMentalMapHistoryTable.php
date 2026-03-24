<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M260324112029AddLocationColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
    private $columnName = 'location';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(10));
        $this->createIndex('{{%idx-mental_map_history-location}}', $this->tableName, 'location');
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-mental_map_history-location}}', $this->tableName);
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
