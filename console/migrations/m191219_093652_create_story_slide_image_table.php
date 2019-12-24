<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_slide_image}}`.
 */
class m191219_093652_create_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_slide_image}}', [
            'id' => $this->primaryKey(),
            'hash' => $this->string()->notNull()->unique(),
            'collection_id' => $this->string()->null(),
            'source_url' => $this->string()->null(),
            'folder' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story_slide_image}}');
    }
}
