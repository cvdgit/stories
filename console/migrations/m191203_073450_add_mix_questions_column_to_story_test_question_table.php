<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test_question}}`.
 */
class m191203_073450_add_mix_questions_column_to_story_test_question_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_test_question}}', 'mix_answers', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story_test_question}}', 'mix_answers');
    }

}
