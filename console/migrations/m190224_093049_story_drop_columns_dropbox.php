<?php

use yii\db\Migration;

/**
 * Class m190224_093049_story_drop_columns_dropbox
 */
class m190224_093049_story_drop_columns_dropbox extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%story}}', 'dropbox_sync_date');
        $this->dropColumn('{{%story}}', 'dropbox_story_filename');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190224_093049_story_drop_columns_dropbox cannot be reverted.\n";
        $this->addColumn('{{%story}}', 'dropbox_sync_date', $this->integer()->null());
        $this->addColumn('{{%story}}', 'dropbox_story_filename', $this->string(255)->null());
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190224_093049_story_drop_columns_dropbox cannot be reverted.\n";

        return false;
    }
    */
}
