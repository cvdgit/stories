<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `payment`.
 */
class m181106_054124_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%payment}}', [
            'id' => $this->primaryKey(),
            'payment' => $this->dateTime()->notNull(),
            'finish' => $this->dateTime()->notNull(),
            'state' => $this->string(255),
            'user_id' => $this->integer()->notNull(),
            'rate_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey(
            'fk_payment_user_id',
            '{{%payment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_payment_rate_id',
            '{{%payment}}',
            'rate_id',
            '{{%rate}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%payment}} CASCADE');
        $this->dropForeignKey('fk_payment_rate_id', '{{%payment}}');
        $this->dropForeignKey('fk_payment_user_id', '{{%payment}}');
        $this->dropTable('payment');
    }

}
