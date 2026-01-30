<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%mental_map_story_slide}}`.
 */
class M260129101456AddRequiredColumnToMentalMapStorySlideTable extends Migration
{
    private $tableName = '{{%mental_map_story_slide}}';
    private $column = 'required';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->column, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->column);
    }
}
