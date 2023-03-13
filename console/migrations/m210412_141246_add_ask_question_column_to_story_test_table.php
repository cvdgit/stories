<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m210412_141246_add_ask_question_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $tableColumnName = 'ask_question';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->tableColumnName);
    }
}
