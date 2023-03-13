<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_story_history}}`.
 */
class m191111_064912_add_percent_column_to_user_story_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_story_history}}', 'percent', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_story_history}}', 'percent');
    }
}
