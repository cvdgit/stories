<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide_image}}`.
 */
class m200109_094522_add_status_column_to_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide_image}}', 'status', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide_image}}', 'status');
    }
}
