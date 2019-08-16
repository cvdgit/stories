<?php

use yii\db\Migration;

/**
 * Class m190811_070901_create_table_story_test
 */
class m190811_070901_create_table_story_test extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_test}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->string()->notNull()->defaultValue(''),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
