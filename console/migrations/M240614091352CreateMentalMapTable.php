<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map}}`.
 */
class M240614091352CreateMentalMapTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mental_map}}', [
            'uuid' => $this->string(36)->notNull(),
            'name' => $this->string()->notNull(),
            'payload' => $this->json()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY (uuid)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mental_map}}');
    }
}
