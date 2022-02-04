<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_list}}`.
 */
class m220203_111856_create_story_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_list}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%story_list}}');
    }
}
