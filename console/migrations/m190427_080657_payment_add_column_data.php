<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190427_080657_payment_add_column_data
 */
class m190427_080657_payment_add_column_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment}}', 'data', $this->string(1024)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190427_080657_payment_add_column_data cannot be reverted.\n";
        $this->dropColumn('{{%payment}}', 'data');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190427_080657_payment_add_column_data cannot be reverted.\n";

        return false;
    }
    */
}
