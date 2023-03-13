<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lesson_block}}`.
 */
class m220411_081010_create_lesson_block_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lesson_block}}', [
            'lesson_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(lesson_id, slide_id)',
        ]);

        $this->createIndex(
            '{{%lesson_block-lesson_id}}',
            '{{%lesson_block}}',
            'lesson_id'
        );

        $this->addForeignKey(
            '{{%lesson_block-lesson_id}}',
            '{{%lesson_block}}',
            'lesson_id',
            '{{%lesson}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%lesson_block-slide_id}}',
            '{{%lesson_block}}',
            'slide_id'
        );

        $this->addForeignKey(
            '{{%lesson_block-slide_id}}',
            '{{%lesson_block}}',
            'slide_id',
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
        $this->dropForeignKey('{{%lesson_block-lesson_id}}', '{{%lesson_block}}');
        $this->dropIndex('{{%lesson_block-lesson_id}}', '{{%lesson_block}}');
        $this->dropForeignKey('{{%lesson_block-slide_id}}', '{{%lesson_block}}');
        $this->dropIndex('{{%lesson_block-slide_id}}', '{{%lesson_block}}');
        $this->dropTable('{{%lesson_block}}');
    }
}
