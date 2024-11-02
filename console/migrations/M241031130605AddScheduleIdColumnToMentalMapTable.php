<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map}}`.
 */
class M241031130605AddScheduleIdColumnToMentalMapTable extends Migration
{
    private $tableName = '{{%mental_map}}';

    public function up(): void
    {
        $this->addColumn($this->tableName, 'schedule_id', $this->integer()->null());
        $this->createIndex('{{%idx-mental_map-schedule_id}}', $this->tableName, 'schedule_id');
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-mental_map-schedule_id}}', $this->tableName);
        $this->dropColumn($this->tableName, 'schedule_id');
    }
}
