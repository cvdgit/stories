<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_answer}}`.
 */
class m201112_072048_add_region_id_column_to_story_test_answer_table extends Migration
{

    private $tableName = '{{%story_test_answer}}';
    private $tableColumnName = 'region_id';

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
