<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%retelling_story_slide}}`.
 */
class M250310083935CreateRetellingStorySlideTable extends Migration
{
    private $tableName = '{{%retelling_story_slide}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'slide_id' => $this->integer()->notNull(),
            'retelling_id' => $this->string(36)->notNull(),
            'is_required' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(slide_id, retelling_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-retelling_story_slide-slide_id}}',
            $this->tableName,
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-retelling_story_slide-retelling_id}}', $this->tableName, 'retelling_id');
        $this->addForeignKey(
            '{{%fk-retelling_story_slide-retelling_id}}',
            $this->tableName,
            'retelling_id',
            '{{%retelling}}',
            'id',
            'CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-retelling_story_slide-retelling_id}}', $this->tableName);
        $this->dropIndex('{{%idx-retelling_story_slide-retelling_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-retelling_story_slide-slide_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
