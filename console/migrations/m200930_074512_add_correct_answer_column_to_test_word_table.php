<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test_word}}`.
 */
class m200930_074512_add_correct_answer_column_to_test_word_table extends Migration
{

    private $tableName = '{{%test_word}}';
    private $tableColumnName = 'correct_answer';

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
