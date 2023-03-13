<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m181113_141054_story_add_columns_dropbox
 */
class m181113_141054_story_add_columns_dropbox extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'dropbox_sync_date', $this->integer()->null());
        $this->addColumn('{{%story}}', 'dropbox_story_filename', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181113_141054_story_add_columns_dropbox cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'dropbox_sync_date');
        $this->dropColumn('{{%story}}', 'dropbox_story_filename');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181113_141054_story_add_columns_dropbox cannot be reverted.\n";

        return false;
    }
    */
}
