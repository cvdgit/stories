<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image_link}}`.
 */
class m200113_090409_create_image_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%image_link}}', [
            'image_id' => $this->integer()->notNull(),
            'link_image_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-image_link', '{{%image_link}}', ['image_id', 'link_image_id']);

        $this->addForeignKey(
            'fk-image_link-image_id',
            '{{%image_link}}',
            'image_id',
            '{{%story_slide_image}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-image_link-link_image_id',
            '{{%image_link}}',
            'link_image_id',
            '{{%story_slide_image}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%image_link}}');
        $this->dropForeignKey('fk-image_link-image_id', '{{%image_link}}');
        $this->dropForeignKey('fk-image_link-link_image_id', '{{%image_link}}');
        $this->dropTable('{{%image_link}}');
    }
}
