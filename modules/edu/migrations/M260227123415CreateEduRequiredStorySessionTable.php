<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_required_story_session}}`.
 */
class M260227123415CreateEduRequiredStorySessionTable extends Migration
{
    private $tableName = '{{%edu_required_story_session}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'required_story_id' => $this->string(36)->notNull(),
            'date' => $this->string(10)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'plan' => $this->tinyInteger()->notNull(),
            'fact' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY (required_story_id, date)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_required_story_session-required_story_id}}',
            $this->tableName,
            'required_story_id',
            '{{%edu_required_story}}',
            'id',
            'CASCADE',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-edu_required_story_session-required_story_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
