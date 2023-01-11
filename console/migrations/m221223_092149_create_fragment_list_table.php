<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fragment_list}}`.
 */
class m221223_092149_create_fragment_list_table extends Migration
{
    private $tableName = '{{%fragment_list}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%idx-fragment_list-created_by}}', $this->tableName,'created_by');

        $this->addForeignKey(
            '{{%fk-fragment_list-created_by}}',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-fragment_list-created_by}}', $this->tableName);
        $this->dropIndex('{{%idx-fragment_list-created_by}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
