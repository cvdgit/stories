<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190809_133808_user_add_column_last_activity
 */
class m190809_133808_user_add_column_last_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'last_activity', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'last_activity');
    }
}
