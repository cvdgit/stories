<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_file_history}}`.
 */
class m220712_075840_create_study_file_history_table extends Migration
{

    private $tableName = '{{%study_file_history}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'study_file_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%idx-study_file_history-user_id}}',
            $this->tableName,
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-study_file_history-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-study_file_history-study_file_id}}',
            $this->tableName,
            'study_file_id'
        );

        $this->addForeignKey(
            '{{%fk-study_file_history-study_file_id}}',
            $this->tableName,
            'study_file_id',
            '{{%study_file}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-study_file_history-user_id}}', $this->tableName);
        $this->dropIndex('{{%idx-study_file_history-user_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-study_file_history-study_file_id}}', $this->tableName);
        $this->dropIndex('{{%idx-study_file_history-study_file_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
