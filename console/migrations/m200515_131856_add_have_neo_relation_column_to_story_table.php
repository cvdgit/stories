<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m200515_131856_add_have_neo_relation_column_to_story_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'have_neo_relation', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'have_neo_relation');
    }
}
