<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%story_slide_image}}`.
 */
class m200110_072503_drop_block_id_column_from_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%story_slide_image}}', 'block_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%story_slide_image}}', 'block_id', $this->string()->null());
    }
}
