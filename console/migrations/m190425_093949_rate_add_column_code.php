<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190425_093949_rate_add_column_code
 */
class m190425_093949_rate_add_column_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rate}}', 'code', $this->string(50)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190425_093949_rate_add_column_code cannot be reverted.\n";
        $this->dropColumn('{{%rate}}', 'code');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190425_093949_rate_add_column_code cannot be reverted.\n";

        return false;
    }
    */
}
