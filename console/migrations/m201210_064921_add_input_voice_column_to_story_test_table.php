<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m201210_064921_add_input_voice_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $tableColumnName = 'input_voice';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->tableColumnName);
    }
}
