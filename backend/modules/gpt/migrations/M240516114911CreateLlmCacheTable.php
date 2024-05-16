<?php

namespace backend\modules\gpt\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%llm_cache}}`.
 */
class M240516114911CreateLlmCacheTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%llm_cache}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(50)->notNull()->unique(),
            'content' => $this->text()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%llm_cache}}');
    }
}
