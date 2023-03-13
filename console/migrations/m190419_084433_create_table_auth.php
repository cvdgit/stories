<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190419_084433_create_table_auth
 */
class m190419_084433_create_table_auth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string()->notNull(),
            'source_id' => $this->string()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_auth_user_id',
            '{{%auth}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190419_084433_create_table_auth cannot be reverted.\n";
        $this->truncateTable('{{%auth}} CASCADE');
        $this->dropForeignKey('fk_auth_user_id', '{{%auth}}');
        $this->dropTable('{{%auth}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190419_084433_create_table_auth cannot be reverted.\n";

        return false;
    }
    */
}
