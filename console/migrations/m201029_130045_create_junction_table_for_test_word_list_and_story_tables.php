<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_word_list_story}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%test_word_list}}`
 * - `{{%story}}`
 */
class m201029_130045_create_junction_table_for_test_word_list_and_story_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%test_word_list_story}}', [
            'test_word_list_id' => $this->integer(),
            'story_id' => $this->integer(),
            'PRIMARY KEY(test_word_list_id, story_id)',
        ]);

        // creates index for column `test_word_list_id`
        $this->createIndex(
            '{{%idx-test_word_list_story-test_word_list_id}}',
            '{{%test_word_list_story}}',
            'test_word_list_id'
        );

        // add foreign key for table `{{%test_word_list}}`
        $this->addForeignKey(
            '{{%fk-test_word_list_story-test_word_list_id}}',
            '{{%test_word_list_story}}',
            'test_word_list_id',
            '{{%test_word_list}}',
            'id',
            'CASCADE'
        );

        // creates index for column `story_id`
        $this->createIndex(
            '{{%idx-test_word_list_story-story_id}}',
            '{{%test_word_list_story}}',
            'story_id'
        );

        // add foreign key for table `{{%story}}`
        $this->addForeignKey(
            '{{%fk-test_word_list_story-story_id}}',
            '{{%test_word_list_story}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%test_word_list}}`
        $this->dropForeignKey(
            '{{%fk-test_word_list_story-test_word_list_id}}',
            '{{%test_word_list_story}}'
        );

        // drops index for column `test_word_list_id`
        $this->dropIndex(
            '{{%idx-test_word_list_story-test_word_list_id}}',
            '{{%test_word_list_story}}'
        );

        // drops foreign key for table `{{%story}}`
        $this->dropForeignKey(
            '{{%fk-test_word_list_story-story_id}}',
            '{{%test_word_list_story}}'
        );

        // drops index for column `story_id`
        $this->dropIndex(
            '{{%idx-test_word_list_story-story_id}}',
            '{{%test_word_list_story}}'
        );

        $this->dropTable('{{%test_word_list_story}}');
    }
}
