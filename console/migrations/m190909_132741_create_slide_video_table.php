<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%slide_video}}`.
 */
class m190909_132741_create_slide_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%slide_video}}', [
            'id' => $this->primaryKey(),
            'video_id' => $this->string(255)->notNull()->unique(),
            'title' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%slide_video}}');
    }
}
