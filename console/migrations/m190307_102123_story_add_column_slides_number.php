<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190307_102123_story_add_column_slides_number
 */
class m190307_102123_story_add_column_slides_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'slides_number', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190307_102123_story_add_column_slides_number cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'slides_number');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190307_102123_story_add_column_slides_number cannot be reverted.\n";

        return false;
    }
    */
}
