<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `rate`.
 */
class m181105_180734_create_rate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%rate}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'description' => $this->string(255),
            'cost' => $this->integer()->notNull(),
            'mounth_count' => $this->integer()->notNull(),
            'type' => "ENUM('active', 'archive')",
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181101_090408_create_table_rate cannot be reverted.\n";
        $this->dropTable('{{%rate}}');
        return false;
    }
}
