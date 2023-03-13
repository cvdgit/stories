<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190819_133615_story_slide_add_column_link_slide_id
 */
class m190819_133615_story_slide_add_column_link_slide_id extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide}}', 'link_slide_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide}}', 'link_slide_id');
    }

}
