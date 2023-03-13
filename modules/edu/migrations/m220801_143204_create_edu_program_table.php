<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_program}}`.
 */
class m220801_143204_create_edu_program_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%edu_program}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%edu_program}}');
    }
}
