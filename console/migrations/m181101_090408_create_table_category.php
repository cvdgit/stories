<?php

use yii\db\Migration;

/**
 * Class m181101_090408_create_table_category
 */
class m181101_090408_create_table_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%category}}', [
            'id' => $this->primaryKey(),
            'tree' => $this->integer()->notNull(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'alias' => $this->string(255)->notNull(),
            'description' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181101_090408_create_table_category cannot be reverted.\n";
        $this->dropTable('{{%category}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181101_090408_create_table_category cannot be reverted.\n";

        return false;
    }
    */
}
