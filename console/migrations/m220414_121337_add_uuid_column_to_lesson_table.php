<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%lesson}}`.
 */
class m220414_121337_add_uuid_column_to_lesson_table extends Migration
{

    private $tableName = '{{%lesson}}';
    private $columnName = 'uuid';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(36)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
