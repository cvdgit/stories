<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_required_story}}`.
 */
class M260221093957CreateEduRequiredStoryTable extends Migration
{
    private $tableName = '{{%edu_required_story}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'story_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'started_at' => $this->integer()->notNull(),
            'days' => $this->tinyInteger()->notNull(),
            'status' => $this->string()->notNull()->defaultValue('new'),
            'metadata' => $this->json()->null(),
            'PRIMARY KEY(id)',
        ]);

        $this->createIndex('{{%idx-edu_required_story-story_id}}', $this->tableName, 'story_id');

        $this->addForeignKey(
            '{{%fk-edu_required_story-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-edu_required_story-student_id}}', $this->tableName, 'student_id');

        $this->addForeignKey(
            '{{%fk-edu_required_story-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-edu_required_story-created_by}}', $this->tableName, 'created_by');

        $this->addForeignKey(
            '{{%fk-edu_required_story-created_by}}',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-edu_required_story-created_by}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_required_story-created_by}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_required_story-student_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_required_story-student_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_required_story-story_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_required_story-story_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
