<?php

use yii\db\Migration;

/**
 * Class m181029_115702_sotry_add_cover_column
 */
class m181029_115702_sotry_add_cover_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'cover', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181029_115702_sotry_add_cover_column cannot be reverted.\n";
        $this->dropColumn('{{%story}}', 'cover');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181029_115702_sotry_add_cover_column cannot be reverted.\n";

        return false;
    }
    */
}
