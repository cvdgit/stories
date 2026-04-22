<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class M260422092712AddShowCorrectResponseColumnToStoryTestTable extends Migration
{
    private $tableName = '{{%story_test}}';
    private $column = 'show_correct_response';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->column, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->column);
    }
}
