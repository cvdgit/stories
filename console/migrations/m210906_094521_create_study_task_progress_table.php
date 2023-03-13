<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_task_progress}}`.
 */
class m210906_094521_create_study_task_progress_table extends Migration
{

    private $tableName = '{{%study_task_progress}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'study_task_id' => $this->integer(),
            'user_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'progress' => $this->tinyInteger(3)->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(study_task_id, user_id)',
        ]);

        $this->createIndex(
            '{{%idx-study_task_progress-study_task_id}}',
            $this->tableName,
            'study_task_id'
        );
        $this->addForeignKey(
            '{{%fk-study_task_progress-study_task_id}}',
            $this->tableName,
            'study_task_id',
            '{{%study_task}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-study_task_progress-user_id}}',
            $this->tableName,
            'user_id'
        );
        $this->addForeignKey(
            '{{%fk-study_task_progress-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            '{{%fk-study_task_progress-study_task_id}}',
            $this->tableName
        );
        $this->dropIndex(
            '{{%idx-study_task_progress-study_task_id}}',
            $this->tableName
        );
        $this->dropForeignKey(
            '{{%fk-study_task_progress-user_id}}',
            $this->tableName
        );
        $this->dropIndex(
            '{{%idx-study_task_progress-user_id}}',
            $this->tableName
        );
        $this->dropTable($this->tableName);
    }
}
