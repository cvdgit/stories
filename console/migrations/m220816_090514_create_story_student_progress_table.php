<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_student_progress}}`.
 */
class m220816_090514_create_story_student_progress_table extends Migration
{

    private $tableName = '{{%story_student_progress}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'story_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'progress' => $this->tinyInteger()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY(story_id, student_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-story_student_progress-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-story_student_progress-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-story_student_progress-student_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-story_student_progress-story_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
