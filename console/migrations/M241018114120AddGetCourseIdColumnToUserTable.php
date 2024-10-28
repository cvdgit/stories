<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class M241018114120AddGetCourseIdColumnToUserTable extends Migration
{
    private $tableName = '{{%user}}';
    private $column = 'get_course_id';

    public function up(): void
    {
        $this->addColumn($this->tableName, $this->column, $this->integer()->null());
    }

    public function down(): void
    {
        $this->dropColumn($this->tableName, $this->column);
    }
}
