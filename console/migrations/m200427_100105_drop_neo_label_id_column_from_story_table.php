<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%story}}`.
 */
class m200427_100105_drop_neo_label_id_column_from_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%story}}', 'neo_label_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%story}}', 'neo_label_id', $this->integer()->null());
    }
}
