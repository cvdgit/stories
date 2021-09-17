<?php

use yii\db\Migration;

/**
 * Class m210917_130820_add_hide_question_name_to_story_test_table
 */
class m210917_130820_add_hide_question_name_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $columnName = 'hide_question_name';

    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
