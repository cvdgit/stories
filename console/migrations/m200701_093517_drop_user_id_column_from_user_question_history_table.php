<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%user_question_history}}`.
 */
class m200701_093517_drop_user_id_column_from_user_question_history_table extends Migration
{

    protected $tableName = '{{%user_question_history}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->truncateTable($this->tableName);
        $this->dropForeignKey('fk-user_question_history-user_id', $this->tableName);
        $this->dropColumn($this->tableName, 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn($this->tableName, 'user_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk-user_question_history-user_id',
            '{{%user_question_history}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }
}
