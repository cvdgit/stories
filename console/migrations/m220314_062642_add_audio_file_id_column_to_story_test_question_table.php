<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class m220314_062642_add_audio_file_id_column_to_story_test_question_table extends Migration
{
    
    private $tableName = '{{%story_test_question}}';
    private $columnName = 'audio_file_id';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->integer()->null());
        $this->createIndex(
            '{{%idx-story_test_question-audio_file_id}}',
            $this->tableName,
            'audio_file_id'
        );
        $this->addForeignKey(
            '{{%fk-story_test_question-audio_file_id}}',
            $this->tableName,
            'audio_file_id',
            '{{%audio_file}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-story_test_question-audio_file_id}}', $this->tableName);
        $this->dropIndex('{{%idx-story_test_question-audio_file_id}}', $this->tableName);
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
