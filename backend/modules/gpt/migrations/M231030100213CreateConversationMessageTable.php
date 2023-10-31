<?php

namespace backend\modules\gpt\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%conversation_message}}`.
 */
class M231030100213CreateConversationMessageTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%conversation_message}}', [
            'uuid' => $this->string(36)->notNull(),
            'conversation_uuid' => $this->string(36)->notNull(),
            'payload' => $this->json()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%conversation_message}}');
    }
}
