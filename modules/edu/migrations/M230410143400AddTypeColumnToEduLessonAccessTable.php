<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%edu_lesson_access}}`.
 */
class M230410143400AddTypeColumnToEduLessonAccessTable extends Migration
{
    private $tableName = '{{%edu_lesson_access}}';
    private $columnName = 'access_type';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, "ENUM('access', 'deny') NOT NULL DEFAULT 'access'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
