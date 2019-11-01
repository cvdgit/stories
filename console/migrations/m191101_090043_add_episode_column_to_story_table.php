<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m191101_090043_add_episode_column_to_story_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'episode', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'episode');
    }

}
