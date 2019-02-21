<?php

use yii\db\Migration;

/**
 * Class m190221_081004_story_add_column_views_number
 */
class m190221_081004_story_add_column_views_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'views_number', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190221_081004_story_add_column_views_number cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'views_number');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190221_081004_story_add_column_views_number cannot be reverted.\n";

        return false;
    }
    */
}
