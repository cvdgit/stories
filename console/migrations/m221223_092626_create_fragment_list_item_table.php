<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fragment_list_item}}`.
 */
class m221223_092626_create_fragment_list_item_table extends Migration
{
    private $tableName = '{{%fragment_list_item}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'fragment_list_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%idx-fragment_list_item-fragment_list_id}}', $this->tableName,'fragment_list_id');

        $this->addForeignKey(
            '{{%fk-fragment_list_item-fragment_list_id}}',
            $this->tableName,
            'fragment_list_id',
            '{{%fragment_list}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-fragment_list_item-fragment_list_id}}', $this->tableName);
        $this->dropIndex('{{%idx-fragment_list_item-fragment_list_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
