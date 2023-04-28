<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fragment_image}}`.
 */
class M230422150336CreateFragmentImageTable extends Migration
{
    private $tableName = '{{%fragment_image}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fragment_image}}', [
            'fragment_id' => $this->string(36)->notNull(),
            'image_id' => $this->integer()->notNull(),
            'testing_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fragment_image}}');
    }
}
