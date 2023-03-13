<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m220329_103321_add_repeat_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $columnName = 'repeat';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
