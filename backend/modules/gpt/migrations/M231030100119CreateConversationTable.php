<?php

namespace backend\modules\gpt\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%conversation}}`.
 */
class M231030100119CreateConversationTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%conversation}}', [
            'uuid' => $this->string(36)->notNull(),
            'title' => $this->string()->notNull(),
            'payload' => $this->json()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%conversation}}');
    }
}
