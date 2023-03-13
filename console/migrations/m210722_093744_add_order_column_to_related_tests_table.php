<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%related_tests}}`.
 */
class m210722_093744_add_order_column_to_related_tests_table extends Migration
{

    private $tableName = '{{%related_tests}}';
    private $columnName = 'order';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->smallInteger()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
