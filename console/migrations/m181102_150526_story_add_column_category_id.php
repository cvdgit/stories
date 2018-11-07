<?php

use yii\db\Migration;

/**
 * Class m181102_150526_story_add_column_category_id
 */
class m181102_150526_story_add_column_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'category_id', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181102_150526_story_add_column_category_id cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'category_id');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181102_150526_story_add_column_category_id cannot be reverted.\n";

        return false;
    }
    */
}
