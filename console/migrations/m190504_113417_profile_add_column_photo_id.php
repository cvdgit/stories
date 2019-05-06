<?php

use yii\db\Migration;

/**
 * Class m190504_113417_profile_add_column_photo_id
 */
class m190504_113417_profile_add_column_photo_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%profile}}',
            'photo_id', $this->integer()
        );
        $this->createIndex(
            '{{%idx-profile-photo_id}}',
            '{{%profile}}',
            'photo_id'
        );
        $this->addForeignKey(
            '{{%fk-profile-photo_id}}',
            '{{%profile}}',
            'photo_id',
            '{{%profile_image}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-profile-photo_id}}', '{{%profile}}');
        $this->dropColumn('{{%profile}}', 'photo_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190504_113417_profile_add_column_photo_id cannot be reverted.\n";

        return false;
    }
    */
}
