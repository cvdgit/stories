<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_slide_block}}`.
 */
class m190829_082013_create_story_slide_block_table extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_slide_block}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'slide_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1),
            'title' => $this->string()->notNull(),
            'href' => $this->string()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_slide_block-slide_id',
            $this->tableName,
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
