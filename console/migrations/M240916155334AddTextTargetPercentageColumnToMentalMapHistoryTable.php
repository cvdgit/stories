<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_history}}`.
 */
class M240916155334AddTextTargetPercentageColumnToMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';
    private $columnName = 'text_target_percentage';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
