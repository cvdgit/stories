<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%story_slide_image}}`.
 */
class m200110_072630_drop_slide_id_column_from_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_story_slide_image-slide_id', '{{%story_slide_image}}');
        $this->dropColumn('{{%story_slide_image}}', 'slide_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
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
}
