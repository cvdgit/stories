<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m200921_131607_update_user_question_history_table
 */
class m200921_131607_update_user_question_history_table extends Migration
{

    private $tableName = '{{%user_question_history}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'question_topic_id', $this->integer()->null());
        $this->alterColumn($this->tableName, 'question_topic_name', $this->string()->null());
        $this->alterColumn($this->tableName, 'relation_id', $this->integer()->null());
        $this->alterColumn($this->tableName, 'relation_name', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'question_topic_id', $this->integer()->notNull());
        $this->alterColumn($this->tableName, 'question_topic_name', $this->string()->notNull());
        $this->alterColumn($this->tableName, 'relation_id', $this->integer()->notNull());
        $this->alterColumn($this->tableName, 'relation_name', $this->string()->notNull());
    }

}
