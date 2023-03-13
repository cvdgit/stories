<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%neo_slide_relations}}`.
 */
class m200508_151644_add_related_entity_id_column_to_neo_slide_relations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->truncateTable('{{%neo_slide_relations}}');
        $this->dropForeignKey('fk-neo_slide_relation-slide_id', '{{%neo_slide_relations}}');
        $this->dropPrimaryKey('pk-neo_slide_relations', '{{%neo_slide_relations}}');

        $this->addColumn('{{%neo_slide_relations}}', 'related_entity_id', $this->integer()->notNull());
        $this->addPrimaryKey('pk-neo_slide_relations', '{{%neo_slide_relations}}', ['entity_id', 'relation_id', 'related_entity_id']);
        $this->addForeignKey(
            'fk-neo_slide_relation-slide_id',
            '{{%neo_slide_relations}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%neo_slide_relations}}');
        $this->dropPrimaryKey('pk-neo_slide_relations', '{{%neo_slide_relations}}');
        $this->dropColumn('{{%neo_slide_relations}}', 'related_entity_id');
        $this->addPrimaryKey('pk-neo_slide_relations', '{{%neo_slide_relations}}', ['entity_id', 'relation_id']);
    }
}
