<?php

use yii\db\Migration;

/**
 * Class m181030_093505_user_change_status_default_value
 */
class m181030_093505_user_change_status_default_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user}}', 'status', $this->smallInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181030_093505_user_change_status_default_value cannot be reverted.\n";
        $this->alterColumn('{{%user}}', 'status', $this->smallInteger()->notNull()->defaultValue(10));
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_093505_user_change_status_default_value cannot be reverted.\n";

        return false;
    }
    */
}
