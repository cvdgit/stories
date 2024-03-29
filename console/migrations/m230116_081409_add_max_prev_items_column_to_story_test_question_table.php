<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class m230116_081409_add_max_prev_items_column_to_story_test_question_table extends Migration
{
    private $tableName = '{{%story_test_question}}';
    private $columnName = 'max_prev_items';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
