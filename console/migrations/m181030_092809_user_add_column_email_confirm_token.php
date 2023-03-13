<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m181030_092809_user_add_column_email_confirm_token
 */
class m181030_092809_user_add_column_email_confirm_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'email_confirm_token', $this->string()->unique()->after('email'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181030_092809_user_add_column_email_confirm_token cannot be reverted.\n";
        $this->dropColumn('{{%user}}', 'email_confirm_token');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_092809_user_add_column_email_confirm_token cannot be reverted.\n";

        return false;
    }
    */
}
