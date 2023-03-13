<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190505_223041_profile_image_drop_column_profile_id
 */
class m190505_223041_profile_image_drop_column_profile_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('{{%idx-profile_image-profile_id}}', '{{%profile_image}}');
        $this->dropForeignKey('fk-profile_image-profile_id', '{{%profile_image}}');
        $this->dropColumn('{{%profile_image}}', 'profile_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%profile_image}}','profile_id', $this->integer()->unique()->notNull());
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

}
