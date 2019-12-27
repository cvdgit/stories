<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide_image}}`.
 */
class m191226_082501_add_slide_id_column_to_story_slide_image_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide_image}}', 'slide_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk_story_slide_image-slide_id',
            '{{%story_slide_image}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_story_slide_image-slide_id', '{{%story_slide_image}}');
        $this->dropColumn('{{%story_slide_image}}', 'slide_id');
    }

}
