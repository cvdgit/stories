<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedule}}`.
 */
class m230208_133714_create_schedule_table extends Migration
{
    private $tableName = '{{%schedule}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
