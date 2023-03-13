<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_task_assign}}`.
 */
class m210902_113024_create_study_task_assign_table extends Migration
{

    private $tableName = '{{%study_task_assign}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'study_task_id' => $this->integer(),
            'study_group_id' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'expired_at' => $this->integer()->notNull(),
            'PRIMARY KEY(study_task_id, study_group_id)',
        ]);

        $this->createIndex(
            '{{%idx-study_task_assign-study_task_id}}',
            $this->tableName,
            'study_task_id'
        );

        $this->addForeignKey(
            '{{%fk-study_task_assign-study_task_id}}',
            $this->tableName,
            'study_task_id',
            '{{%study_task}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-study_task_assign-study_group_id}}',
            $this->tableName,
            'study_group_id'
        );

        $this->addForeignKey(
            '{{%fk-study_task_assign-study_group_id}}',
            $this->tableName,
            'study_group_id',
            '{{%study_group}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-study_task_assign-study_task_id}}', $this->tableName);
        $this->dropIndex('{{%idx-study_task_assign-study_task_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-study_task_assign-study_group_id}}', $this->tableName);
        $this->dropIndex('{{%idx-study_task_assign-study_group_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
