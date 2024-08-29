<?php

namespace backend\modules\LearningPath\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%learning_path}}`.
 */
class M240805144347CreateLearningPathTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%learning_path}}', [
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
        $this->dropTable('{{%learning_path}}');
    }
}
