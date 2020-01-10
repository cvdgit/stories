<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide_image}}`.
 */
class m200109_131117_add_block_id_column_to_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide_image}}', 'block_id', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide_image}}', 'block_id');
    }
}
