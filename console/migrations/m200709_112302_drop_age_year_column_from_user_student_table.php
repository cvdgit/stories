<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%user_student}}`.
 */
class m200709_112302_drop_age_year_column_from_user_student_table extends Migration
{

    protected $tableName = '{{%user_student}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn($this->tableName, 'age_year');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn($this->tableName,'age_year', $this->integer()->notNull()->defaultValue(0));
    }

}
