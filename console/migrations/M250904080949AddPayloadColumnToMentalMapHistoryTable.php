<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M250904080949AddPayloadColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';

    public function up(): void
    {
        $this->addColumn($this->tableName, 'all_important_words_included', $this->tinyInteger()->null());
        $this->addColumn($this->tableName, 'payload', $this->json()->null());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, 'payload');
        $this->dropColumn($this->tableName, 'all_important_words_included');
    }
}
