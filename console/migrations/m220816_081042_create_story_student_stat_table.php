<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_student_stat}}`.
 */
class m220816_081042_create_story_student_stat_table extends Migration
{

    private $tableName = '{{%story_student_stat}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'story_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'session' => $this->string(50)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%idx-story_student_stat-story_id}}', $this->tableName,'story_id');
        $this->createIndex('{{%idx-story_student_stat-student_id}}', $this->tableName,'student_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-story_student_stat-story_id}}', $this->tableName);
        $this->dropIndex('{{%idx-story_student_stat-student_id}}', $this->tableName);
        $this->dropTable('{{%story_student_stat}}');
    }
}
