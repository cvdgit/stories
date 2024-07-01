<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map_story_slide}}`.
 */
class M240614101334CreateMentalMapStorySlideTable extends Migration
{
    private $tableName = '{{%mental_map_story_slide}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'mental_map_id' => $this->string(36)->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'PRIMARY KEY (mental_map_id, slide_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-mental_map_story_slide-mental_map_id}}',
            $this->tableName,
            'mental_map_id',
            '{{%mental_map}}',
            'uuid',
            'CASCADE'
        );

        $this->createIndex('{{%idx-mental_map_story_slide-slide_id}}', $this->tableName, 'slide_id');
        $this->addForeignKey(
            '{{%fk-mental_map_story_slide-slide_id}}',
            $this->tableName,
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-mental_map_story_slide-mental_map_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-mental_map_story_slide-slide_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_story_slide-slide_id}}', $this->tableName);
        $this->dropTable('{{%mental_map_story_slide}}');
    }
}
