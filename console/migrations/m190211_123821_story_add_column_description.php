<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190211_123821_story_add_column_description
 */
class m190211_123821_story_add_column_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'description', $this->string(1024)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190211_123821_story_add_column_description cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'description');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190211_123821_story_add_column_description cannot be reverted.\n";

        return false;
    }
    */
}
