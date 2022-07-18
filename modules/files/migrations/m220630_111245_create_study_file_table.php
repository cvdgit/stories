<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_file}}`.
 */
class m220630_111245_create_study_file_table extends Migration
{

    private $tableName = '{{%study_file}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'uuid' => $this->string(36)->unique(),
            'name' => $this->string()->notNull(),
            'alias' => $this->string(),
            'folder_id' => $this->integer()->notNull(),
            'type' => $this->string(50)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
        ]);

        $this->createIndex(
            '{{%idx-study_file-folder_id}}',
            $this->tableName,
            'folder_id'
        );

        $this->addForeignKey(
            '{{%fk-study_file-folder_id}}',
            $this->tableName,
            'folder_id',
            '{{%study_folder}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-study_file-folder_id}}', $this->tableName);
        $this->dropIndex('{{%idx-study_file-folder_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
