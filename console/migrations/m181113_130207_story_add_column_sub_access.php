<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m181113_130207_story_add_column_sub_access
 */
class m181113_130207_story_add_column_sub_access extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'sub_access', $this->smallInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181113_130207_story_add_column_sub_access cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'sub_access');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181113_130207_story_add_column_sub_access cannot be reverted.\n";

        return false;
    }
    */
}
