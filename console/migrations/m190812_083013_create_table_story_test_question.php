<?php

use yii\db\Migration;

/**
 * Class m190812_083013_create_table_story_test_question
 */
class m190812_083013_create_table_story_test_question extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_test_question}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'story_test_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'order' => $this->smallInteger()->notNull()->defaultValue(0),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_test_answer-story_test_id',
            $this->tableName,
            'story_test_id',
            '{{%story_test}}',
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
