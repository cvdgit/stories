<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide}}`.
 */
class M260513115638AddSettingsColumnToStorySlideTable extends Migration
{
    private $tableName = '{{%story_slide}}';
    private $columnName = 'settings';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->json()->null());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
