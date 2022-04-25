<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%lesson}}`.
 */
class m220418_113747_add_type_column_to_lesson_table extends Migration
{

    private $tableName = '{{%lesson}}';
    private $columnName = 'type';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
