<?php

namespace backend\modules\gpt\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%llm_feedback}}`.
 */
class M240117095021CreateLlmFeedbackTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%llm_feedback}}', [
            'id' => $this->primaryKey(),
            'target' => $this->string(50)->notNull(),
            'run_id' => $this->string(36)->notNull()->unique(),
            'input' => $this->json()->notNull(),
            'output' => $this->json()->notNull(),
            'user_id' => $this->integer()->null(),
            'score' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%llm_feedback}}');
    }
}
