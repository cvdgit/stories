<?php

use yii\db\Migration;

/**
 * Class m190718_082851_create_table_story_slides
 */
class m190718_082851_create_table_story_slide extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_slide}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer()->notNull(),
            'data' => $this->text()->notNull(),
            'number' => $this->smallInteger()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);

        $this->addForeignKey(
            'fk_story_slide-story_id',
            $this->tableName,
            'story_id',
            '{{%story}}',
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
