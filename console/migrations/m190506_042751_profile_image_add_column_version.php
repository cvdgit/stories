<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m190506_042751_profile_image_add_column_version
 */
class m190506_042751_profile_image_add_column_version extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile_image}}', 'version', $this->smallInteger()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile_image}}', 'version');
    }

}
