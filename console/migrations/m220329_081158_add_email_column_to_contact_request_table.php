<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%contact_request}}`.
 */
class m220329_081158_add_email_column_to_contact_request_table extends Migration
{

    private $tableName = '{{%contact_request}}';
    private $columnName = 'email';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string()->notNull()->after('phone'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
