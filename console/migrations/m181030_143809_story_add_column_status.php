<?php

use yii\db\Migration;

/**
 * Class m181030_143809_story_add_column_status
 */
class m181030_143809_story_add_column_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'status', $this->smallInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181030_143809_story_add_column_status cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'status');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_143809_story_add_column_status cannot be reverted.\n";

        return false;
    }
    */
}
