<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%neo_slide_relations}}`.
 */
class m200312_082427_create_neo_slide_relations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%neo_slide_relations}}', [
            'slide_id' => $this->integer()->notNull(),
            'entity_id' => $this->integer()->notNull(),
            'relation_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('pk-neo_slide_relations', '{{%neo_slide_relations}}', ['slide_id', 'entity_id', 'relation_id']);

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
        $this->dropTable('{{%neo_slide_relations}}');
    }
}
