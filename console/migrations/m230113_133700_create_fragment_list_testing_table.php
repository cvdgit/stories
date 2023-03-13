<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fragment_list_testing}}`.
 */
class m230113_133700_create_fragment_list_testing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fragment_list_testing}}', [
            'fragment_list_id' => $this->integer()->notNull(),
            'testing_id' => $this->integer()->notNull(),
            'PRIMARY KEY(fragment_list_id, testing_id)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fragment_list_testing}}');
    }
}
