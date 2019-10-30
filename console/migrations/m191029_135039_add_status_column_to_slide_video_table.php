<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%slide_video}}`.
 */
class m191029_135039_add_status_column_to_slide_video_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%slide_video}}', 'status', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%slide_video}}', 'status');
    }

}
