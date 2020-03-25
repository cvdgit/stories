<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m200325_081836_add_neo_label_id_column_to_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'neo_label_id', $this->integer()->null()->after('published_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'neo_label_id');
    }
}
