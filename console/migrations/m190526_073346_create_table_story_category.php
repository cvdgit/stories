<?php

use yii\db\Migration;

/**
 * Class m190526_073346_create_table_story_category
 */
class m190526_073346_create_table_story_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%story_category}}', [
            'story_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-story_category', '{{%story_category}}', ['story_id', 'category_id']);

        $this->addForeignKey(
            'fk-story_category-story_id',
            '{{%story_category}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-story_category-category_id',
            '{{%story_category}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%story_category}}');
        $this->dropForeignKey('fk-story_category-story_id', '{{%story_category}}');
        $this->dropForeignKey('fk-story_category-category_id', '{{%story_category}}');
        $this->dropTable('{{%story_category}}');
    }

}
