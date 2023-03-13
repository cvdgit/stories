<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190504_100009_create_table_profile_image
 */
class m190504_100009_create_table_profile_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%profile_image}}', [
            'id' => $this->primaryKey(),
            'profile_id' => $this->integer()->unique()->notNull(),
            'file' => $this->string()->notNull(),
        ], $tableOptions);

        $this->createIndex('{{%idx-profile_image-profile_id}}', '{{%profile_image}}', 'profile_id');

        $this->addForeignKey(
            'fk-profile_image-profile_id',
            '{{%profile_image}}',
            'profile_id',
            '{{%profile}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%profile_image}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190504_100009_create_table_profile_image cannot be reverted.\n";

        return false;
    }
    */
}
