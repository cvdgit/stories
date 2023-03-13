<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_student}}`.
 */
class m200709_085348_add_birth_date_column_to_user_student_table extends Migration
{

    protected $tableName = '{{%user_student}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'birth_date', $this->date()->defaultValue('1970-01-01'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'birth_date');
    }

}
