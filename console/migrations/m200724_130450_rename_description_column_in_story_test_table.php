<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m200724_130450_rename_description_column_in_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $tableColumnName = 'header';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn($this->tableName, 'description', $this->tableColumnName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn($this->tableName, $this->tableColumnName, 'description');
    }

}
