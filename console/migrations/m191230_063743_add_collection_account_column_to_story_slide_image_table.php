<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide_image}}`.
 */
class m191230_063743_add_collection_account_column_to_story_slide_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide_image}}', 'collection_account', $this->string()->null()->after('hash'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide_image}}', 'collection_account');
    }
}
