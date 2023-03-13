<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_feedback}}`.
 */
class m191125_145342_add_slide_id_column_to_story_feedback_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_feedback}}', 'slide_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk_story_feedback-slide_id',
            '{{%story_feedback}}',
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_story_feedback-slide_id', '{{%story_feedback}}');
        $this->dropColumn('{{%story_feedback}}', 'slide_id');
    }

}
