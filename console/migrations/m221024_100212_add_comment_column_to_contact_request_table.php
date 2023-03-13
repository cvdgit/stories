<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%contact_request}}`.
 */
class m221024_100212_add_comment_column_to_contact_request_table extends Migration
{
    private $columnName = 'comment';
    private $tableName = '{{%contact_request}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
