<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class}}`.
 */
class m220728_170119_create_edu_class_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%edu_class}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%edu_class}}');
    }
}
