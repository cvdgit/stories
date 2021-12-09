<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class m211209_081704_add_hint_column_to_story_test_question_table extends Migration
{

    private $tableName = '{{%story_test_question}}';
    private $columnName = 'hint';

    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(255)->null());
    }

    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
