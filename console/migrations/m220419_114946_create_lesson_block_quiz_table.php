<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lesson_block_quiz}}`.
 */
class m220419_114946_create_lesson_block_quiz_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lesson_block_quiz}}', [
            'lesson_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'quiz_id' => $this->integer()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(lesson_id, slide_id, quiz_id)',
        ]);

        $this->createIndex(
            '{{%lesson_block_quiz-lesson_id}}',
            '{{%lesson_block_quiz}}',
            'lesson_id'
        );

        $this->addForeignKey(
            '{{%lesson_block_quiz-lesson_id}}',
            '{{%lesson_block_quiz}}',
            'lesson_id',
            '{{%lesson}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%lesson_block_quiz-slide_id}}',
            '{{%lesson_block_quiz}}',
            'slide_id'
        );

        $this->addForeignKey(
            '{{%lesson_block_quiz-slide_id}}',
            '{{%lesson_block_quiz}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%lesson_block_quiz-quiz_id}}',
            '{{%lesson_block_quiz}}',
            'quiz_id'
        );

        $this->addForeignKey(
            '{{%lesson_block_quiz-quiz_id}}',
            '{{%lesson_block_quiz}}',
            'quiz_id',
            '{{%story_test}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%lesson_block_quiz-lesson_id}}', '{{%lesson_block_quiz}}');
        $this->dropIndex('{{%lesson_block_quiz-lesson_id}}', '{{%lesson_block_quiz}}');
        $this->dropForeignKey('{{%lesson_block_quiz-slide_id}}', '{{%lesson_block_quiz}}');
        $this->dropIndex('{{%lesson_block_quiz-slide_id}}', '{{%lesson_block_quiz}}');
        $this->dropForeignKey('{{%lesson_block_quiz-quiz_id}}', '{{%lesson_block_quiz}}');
        $this->dropIndex('{{%lesson_block_quiz-quiz_id}}', '{{%lesson_block_quiz}}');
        $this->dropTable('{{%lesson_block_quiz}}');
    }
}
