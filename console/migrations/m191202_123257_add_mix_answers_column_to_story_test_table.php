<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m191202_123257_add_mix_answers_column_to_story_test_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_test}}', 'mix_answers', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_test}}', 'mix_answers');
    }

}
