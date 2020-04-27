<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%story}}`.
 */
class m200427_100211_drop_neo_entity_id_column_from_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%story}}', 'neo_entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%story}}', 'neo_entity_id', $this->integer()->null());
    }
}
