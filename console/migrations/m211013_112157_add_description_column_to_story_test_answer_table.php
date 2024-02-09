<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_answer}}`.
 */
class m211013_112157_add_description_column_to_story_test_answer_table extends Migration
{

    private $tableName = '{{%story_test_answer}}';
    private $columnName = 'description';

    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(2048)->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
