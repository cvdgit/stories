<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class M251120073913AddIsScreenRecorderColumnToStoryTable extends Migration
{
    private $tableName = '{{%story}}';
    private $column = 'is_screen_recorder';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->column, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->column);
    }
}
