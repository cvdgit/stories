<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_audio_track}}`.
 */
class m191126_102347_add_status_column_to_story_audio_track_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_audio_track}}', 'status', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_audio_track}}', 'status');
    }

}
