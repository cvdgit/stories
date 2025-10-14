<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_story_slide}}`.
 */
class M251014105008AddBlockIdColumnToMentalMapStorySlideTable extends Migration
{
    private $tableName = '{{%mental_map_story_slide}}';
    private $columnName = 'block_id';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(36)->notNull());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
