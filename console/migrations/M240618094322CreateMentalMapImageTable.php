<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map_image}}`.
 */
class M240618094322CreateMentalMapImageTable extends Migration
{
    private $tableName = '{{%mental_map_image}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'uuid' => $this->string(36)->notNull(),
            'name' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'key' => $this->string(1024)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'mental_map_id' => $this->string(36)->null(),
            'PRIMARY KEY(uuid)',
        ]);

        $this->createIndex('{{%idx-mental_map_image-mental_map_id}}', $this->tableName, 'mental_map_id');
        $this->addForeignKey(
            '{{%fk-mental_map_image-mental_map_id}}',
            $this->tableName,
            'mental_map_id',
            '{{%mental_map}}',
            'uuid',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-mental_map_image-mental_map_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_image-mental_map_id}}', $this->tableName);
        $this->dropTable('{{%mental_map_image}}');
    }
}
