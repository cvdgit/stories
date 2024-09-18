<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%llm_prompt}}`.
 */
class M240918072511CreateLlmPromptTable extends Migration
{
    private $tableName = '{{%llm_prompt}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'name' => $this->string()->notNull(),
            'prompt' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'key' => $this->string()->notNull(),
            'PRIMARY KEY (`id`)',
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
