<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m230209_131330_add_schedule_id_column_to_story_test_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_test}}', 'schedule_id', $this->integer()->null());
        $this->createIndex('{{%idx-story_test-schedule_id}}', '{{%story_test}}', 'schedule_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-story_test-schedule_id}}', '{{%story_test}}');
        $this->dropColumn('{{%story_test}}', 'schedule_id');
    }
}
