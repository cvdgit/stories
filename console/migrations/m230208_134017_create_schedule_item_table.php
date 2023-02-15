<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%schedule_item}}`.
 */
class m230208_134017_create_schedule_item_table extends Migration
{
    private $tableName = '{{%schedule_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->integer()->notNull(),
            'hours' => $this->tinyInteger()->notNull(),
        ]);
        $this->addForeignKey(
            '{{%fk-schedule_item-schedule_id}}',
            $this->tableName,
            'schedule_id',
            '{{%schedule}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-schedule_item-schedule_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
