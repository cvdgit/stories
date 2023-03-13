<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190611_194657_create_table_news
 */
class m190611_194657_create_table_news extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%news}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'slug' => $this->string()->notNull()->defaultValue(''),
            'text' => $this->text()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(
            'fk_news_user_id',
            $this->tableName,
            'user_id',
            '{{%user}}',
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
