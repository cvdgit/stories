<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_list_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%story_list}}`
 * - `{{%category}}`
 */
class m220203_112139_create_junction_table_for_story_list_and_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_list_category}}', [
            'story_list_id' => $this->integer(),
            'category_id' => $this->integer(),
            'PRIMARY KEY(story_list_id, category_id)',
        ]);

        // creates index for column `story_list_id`
        $this->createIndex(
            '{{%idx-story_list_category-story_list_id}}',
            '{{%story_list_category}}',
            'story_list_id'
        );

        // add foreign key for table `{{%story_list}}`
        $this->addForeignKey(
            '{{%fk-story_list_category-story_list_id}}',
            '{{%story_list_category}}',
            'story_list_id',
            '{{%story_list}}',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            '{{%idx-story_list_category-category_id}}',
            '{{%story_list_category}}',
            'category_id'
        );

        // add foreign key for table `{{%category}}`
        $this->addForeignKey(
            '{{%fk-story_list_category-category_id}}',
            '{{%story_list_category}}',
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
        // drops foreign key for table `{{%story_list}}`
        $this->dropForeignKey(
            '{{%fk-story_list_category-story_list_id}}',
            '{{%story_list_category}}'
        );

        // drops index for column `story_list_id`
        $this->dropIndex(
            '{{%idx-story_list_category-story_list_id}}',
            '{{%story_list_category}}'
        );

        // drops foreign key for table `{{%category}}`
        $this->dropForeignKey(
            '{{%fk-story_list_category-category_id}}',
            '{{%story_list_category}}'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            '{{%idx-story_list_category-category_id}}',
            '{{%story_list_category}}'
        );

        $this->dropTable('{{%story_list_category}}');
    }
}
