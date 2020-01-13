<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%image_slide_block}}`.
 */
class m200110_070300_create_image_slide_block_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%image_slide_block}}', [
            'image_id' => $this->integer()->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'block_id' => $this->string()->notNull(),
        ]);

        $this->addPrimaryKey('pk-image_slide_block', '{{%image_slide_block}}', ['image_id', 'slide_id', 'block_id']);

        $this->addForeignKey(
            'fk-image_slide_block-image_id',
            '{{%image_slide_block}}',
            'image_id',
            '{{%story_slide_image}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-image_slide_block-slide_id',
            '{{%image_slide_block}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%image_slide_block}}');
        $this->dropForeignKey('fk-image_slide_block-image_id', '{{%image_slide_block}}');
        $this->dropForeignKey('fk-image_slide_block-slide_id', '{{%image_slide_block}}');
        $this->dropTable('{{%image_slide_block}}');
    }
}
