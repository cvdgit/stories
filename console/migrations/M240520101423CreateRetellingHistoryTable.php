<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%retelling_history}}`.
 */
class M240520101423CreateRetellingHistoryTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%retelling_history}}', [
            'story_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'key' => $this->string(50)->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'overall_similarity' => $this->tinyInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'PRIMARY KEY (`story_id`, `user_id`, `key`)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%retelling_history}}');
    }
}
