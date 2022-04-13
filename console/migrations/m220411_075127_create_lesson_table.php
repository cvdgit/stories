<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lesson}}`.
 */
class m220411_075127_create_lesson_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lesson}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'story_id' => $this->integer()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            '{{%idx-lesson-story_id}}',
            '{{%lesson}}',
            'story_id'
        );

        $this->addForeignKey(
            '{{%fk-lesson-story_id}}',
            '{{%lesson}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-lesson-story_id}}', '{{%lesson}}');
        $this->dropIndex('{{%idx-lesson-story_id}}', '{{%lesson}}');
        $this->dropTable('{{%lesson}}');
    }
}
