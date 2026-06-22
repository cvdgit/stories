<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%retelling}}`.
 */
class M260619125006AddPayloadColumnToRetellingTable extends Migration
{
    private $tableName = '{{%retelling}}';
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
