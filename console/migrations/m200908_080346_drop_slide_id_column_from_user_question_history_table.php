<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%user_question_history}}`.
 */
class m200908_080346_drop_slide_id_column_from_user_question_history_table extends Migration
{

    private $tableName = '{{%user_question_history}}';
    private $tableColumnName = 'slide_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-user_question_history-slide_id', $this->tableName);
        $this->dropColumn($this->tableName, $this->tableColumnName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->integer()->notNull());
        $this->addForeignKey(
            'fk-user_question_history-slide_id',
            $this->tableName,
            $this->tableColumnName,
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }
}
