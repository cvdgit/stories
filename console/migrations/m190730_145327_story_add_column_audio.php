<?php

use yii\db\Migration;

/**
 * Class m190730_145327_story_add_column_audio
 */
class m190730_145327_story_add_column_audio extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'audio', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'audio');
    }

}
