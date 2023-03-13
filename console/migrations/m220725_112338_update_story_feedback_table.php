<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m220725_112338_update_story_feedback_table
 */
class m220725_112338_update_story_feedback_table extends Migration
{

    private $tableName = '{{%story_feedback}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'text', $this->string(1024));
        $this->addColumn($this->tableName, 'testing_id', $this->integer()->null());
        $this->addColumn($this->tableName, 'question_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->alterColumn($this->tableName, 'text', $this->string(255));
        $this->dropColumn($this->tableName, 'testing_id');
        $this->dropColumn($this->tableName, 'question_id');
    }
}
