<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class m201109_072907_add_image_column_to_story_test_question_table extends Migration
{

    private $tableName = '{{%story_test_question}}';
    private $tableColumnName = 'image';

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
