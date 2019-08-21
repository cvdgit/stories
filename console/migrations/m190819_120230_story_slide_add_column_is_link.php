<?php

use yii\db\Migration;

/**
 * Class m190819_120230_story_slide_add_column_is_link
 */
class m190819_120230_story_slide_add_column_is_link extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_slide}}', 'is_link', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_slide}}', 'is_link');
    }

}
