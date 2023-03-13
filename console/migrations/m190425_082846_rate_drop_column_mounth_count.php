<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190425_082846_rate_drop_column_mounth_count
 */
class m190425_082846_rate_drop_column_mounth_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%rate}}', 'mounth_count');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190425_082846_rate_drop_column_mounth_count cannot be reverted.\n";
        $this->addColumn('{{%rate}}', 'mounth_count', $this->integer()->notNull());
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190425_082846_rate_drop_column_mounth_count cannot be reverted.\n";

        return false;
    }
    */
}
