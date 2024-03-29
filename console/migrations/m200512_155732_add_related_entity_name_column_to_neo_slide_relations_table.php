<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%neo_slide_relations}}`.
 */
class m200512_155732_add_related_entity_name_column_to_neo_slide_relations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%neo_slide_relations}}', 'related_entity_name', $this->string()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%neo_slide_relations}}', 'related_entity_name');
    }
}
