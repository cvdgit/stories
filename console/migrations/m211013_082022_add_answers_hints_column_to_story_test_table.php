<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m211013_082022_add_answers_hints_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $columnName = 'answers_hints';

    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
