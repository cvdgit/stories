<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%audio_file}}`.
 */
class m220314_062126_create_audio_file_table extends Migration
{

    private $tableName = '{{%audio_file}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'folder' => $this->string()->notNull(),
            'audio_file' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
