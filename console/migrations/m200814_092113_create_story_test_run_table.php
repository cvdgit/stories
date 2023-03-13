<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_test_run}}`.
 */
class m200814_092113_create_story_test_run_table extends Migration
{

    private $tableName = '{{%story_test_run}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'test_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey(
            'fk-story_test_run-test_id',
            $this->tableName,
            'test_id',
            '{{%story_test}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-story_test_run-student_id',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
