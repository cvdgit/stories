<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%story}}`.
 */
class m200427_100304_drop_neo_entity_name_column_from_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%story}}', 'neo_entity_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%story}}', 'neo_entity_name', $this->string()->null());
    }
}
