<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M241101132558AlterMentalMapHistoryTable
 */
class M241101132558AlterMentalMapHistoryTable extends Migration
{
    private $tableName = '{{%mental_map_history}}';

    public function up(): void
    {
        $this->dropForeignKey('{{%fk-mental_map_history-story_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_history-story_id}}', $this->tableName);

        $this->alterColumn($this->tableName, 'story_id', $this->integer()->null());
        $this->alterColumn($this->tableName, 'slide_id', $this->integer()->null());
    }

    public function down(): void
    {
        $this->alterColumn($this->tableName, 'story_id', $this->integer()->notNull());
        $this->alterColumn($this->tableName, 'slide_id', $this->integer()->notNull());

        $this->createIndex('{{%idx-mental_map_history-story_id}}', $this->tableName, 'story_id');
        $this->addForeignKey(
            '{{%fk-mental_map_history-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );
    }
}
