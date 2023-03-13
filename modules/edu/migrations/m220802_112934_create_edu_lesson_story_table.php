<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_lesson_story}}`.
 */
class m220802_112934_create_edu_lesson_story_table extends Migration
{

    private $tableName = '{{%edu_lesson_story}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'lesson_id' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(lesson_id, story_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_lesson_story-lesson_id}}',
            $this->tableName,
            'lesson_id',
            '{{%edu_lesson}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-edu_lesson_story-story_id}}',
            $this->tableName,
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
        $this->dropForeignKey('{{%fk-edu_lesson_story-story_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_lesson_story-lesson_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
