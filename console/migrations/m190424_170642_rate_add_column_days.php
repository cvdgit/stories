<?php

use yii\db\Migration;

/**
 * Class m190424_170642_rate_add_column_days
 */
class m190424_170642_rate_add_column_days extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%rate}}', 'days', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190424_170642_rate_add_column_days cannot be reverted.\n";
        $this->dropColumn('{{%rate}}', 'days');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190424_170642_rate_add_column_days cannot be reverted.\n";

        return false;
    }
    */
}
