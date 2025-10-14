<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%speech_trainer}}`.
 */
class M251014074351CreateSpeechTrainerTable extends Migration
{
    private $tableName = '{{%speech_trainer}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'name' => $this->string()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'block_id' => $this->string(36)->notNull(),
            'retelling_slide_id' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY(id)',
        ]);

        $this->createIndex('{{%idx-speech_trainer-unique}}', $this->tableName, ['slide_id', 'block_id'], true);
        $this->addForeignKey(
            '{{%fk-speech_trainer-slide_id}}',
            $this->tableName,
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-speech_trainer-slide_id}}', $this->tableName);
        $this->dropIndex('{{%idx-speech_trainer-unique}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
