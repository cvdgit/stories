<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190812_101153_create_table_story_test_answer
 */
class m190812_101153_create_table_story_test_answer extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_test_answer}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'story_question_id' => $this->integer()->notNull(),
            'name' => $this->string(512)->notNull(),
            'order' => $this->smallInteger()->notNull()->defaultValue(0),
            'is_correct' => $this->tinyInteger()->notNull()->defaultValue(0),
            'image' => $this->string()->null(),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_test_answer-story_test_question',
            $this->tableName,
            'story_question_id',
            '{{%story_test_question}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
