<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m191102_103911_add_video_column_to_story_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'video', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'video');
    }

}
