<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190211_145714_story_add_column_source_id
 */
class m190211_145714_story_add_column_source_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'source_id', $this->smallInteger()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190211_145714_story_add_column_source_id cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'source_id');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190211_145714_story_add_column_source_id cannot be reverted.\n";

        return false;
    }
    */
}
