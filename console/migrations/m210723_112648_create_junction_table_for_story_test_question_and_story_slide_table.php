<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_test_question_story_slide}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%story_test_question}}`
 * - `{{%story_slide}}`
 */
class m210723_112648_create_junction_table_for_story_test_question_and_story_slide_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_test_question_story_slide}}', [
            'story_test_question_id' => $this->integer(),
            'story_slide_id' => $this->integer(),
            'sort_order' => $this->smallInteger()->defaultValue(1),
            'PRIMARY KEY(story_test_question_id, story_slide_id)',
        ]);

        // creates index for column `story_test_question_id`
        $this->createIndex(
            '{{%idx-story_test_question_story_slide-story_test_question_id}}',
            '{{%story_test_question_story_slide}}',
            'story_test_question_id'
        );

        // add foreign key for table `{{%story_test_question}}`
        $this->addForeignKey(
            '{{%fk-story_test_question_story_slide-story_test_question_id}}',
            '{{%story_test_question_story_slide}}',
            'story_test_question_id',
            '{{%story_test_question}}',
            'id',
            'CASCADE'
        );

        // creates index for column `story_slide_id`
        $this->createIndex(
            '{{%idx-story_test_question_story_slide-story_slide_id}}',
            '{{%story_test_question_story_slide}}',
            'story_slide_id'
        );

        // add foreign key for table `{{%story_slide}}`
        $this->addForeignKey(
            '{{%fk-story_test_question_story_slide-story_slide_id}}',
            '{{%story_test_question_story_slide}}',
            'story_slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%story_test_question}}`
        $this->dropForeignKey(
            '{{%fk-story_test_question_story_slide-story_test_question_id}}',
            '{{%story_test_question_story_slide}}'
        );

        // drops index for column `story_test_question_id`
        $this->dropIndex(
            '{{%idx-story_test_question_story_slide-story_test_question_id}}',
            '{{%story_test_question_story_slide}}'
        );

        // drops foreign key for table `{{%story_slide}}`
        $this->dropForeignKey(
            '{{%fk-story_test_question_story_slide-story_slide_id}}',
            '{{%story_test_question_story_slide}}'
        );

        // drops index for column `story_slide_id`
        $this->dropIndex(
            '{{%idx-story_test_question_story_slide-story_slide_id}}',
            '{{%story_test_question_story_slide}}'
        );

        $this->dropTable('{{%story_test_question_story_slide}}');
    }
}
