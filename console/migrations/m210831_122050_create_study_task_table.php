<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_task}}`.
 */
class m210831_122050_create_study_task_table extends Migration
{

    private $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    private $tableName = '{{%study_task}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $this->tableOptions);

        $this->addForeignKey(
            'fk_study_task-created_by',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_study_task-updated_by',
            $this->tableName,
            'updated_by',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_study_task-story_id',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_study_task-created_by', $this->tableName);
        $this->dropForeignKey('fk_study_task-updated_by', $this->tableName);
        $this->dropForeignKey('fk_study_task-story_id', $this->tableName);
        $this->dropTable('{{%study_task}}');
    }
}
