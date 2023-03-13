<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m200306_113737_add_neo_entity_name_column_to_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'neo_entity_name', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'neo_entity_name');
    }
}
