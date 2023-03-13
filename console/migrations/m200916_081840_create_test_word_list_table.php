<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_word_list}}`.
 */
class m200916_081840_create_test_word_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%test_word_list}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%test_word_list}}');
    }
}
