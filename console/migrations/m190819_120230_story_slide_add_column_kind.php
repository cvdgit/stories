<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190819_120230_story_slide_add_column_kind
 */
class m190819_120230_story_slide_add_column_kind extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide}}', 'kind', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide}}', 'kind');
    }

}
