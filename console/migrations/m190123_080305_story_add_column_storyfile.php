<?php

use yii\db\Migration;

/**
 * Class m190123_080305_story_add_column_storyfile
 */
class m190123_080305_story_add_column_storyfile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'story_file', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190123_080305_story_add_column_storyfile cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'story_file');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190123_080305_story_add_column_storyfile cannot be reverted.\n";

        return false;
    }
    */
}
