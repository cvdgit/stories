<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_question_history}}`.
 */
class m201103_075427_add_stars_column_to_user_question_history_table extends Migration
{

    private $tableName = '{{%user_question_history}}';
    private $tableColumnName = 'stars';

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
