<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class_book}}`.
 */
class m220804_084934_create_edu_class_book_table extends Migration
{

    private $tableName = '{{%edu_class_book}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%edu_class_book}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'class_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%idx-edu_class_book-user_id}}',
            $this->tableName,
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-edu_class_book-user_id}}',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-edu_class_book-class_id}}',
            $this->tableName,
            'class_id'
        );

        $this->addForeignKey(
            '{{%fk-edu_class_book-class_id}}',
            $this->tableName,
            'class_id',
            '{{%edu_class}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_class_book-user_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_class_book-class_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_class_book-user_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_class_book-class_id}}', $this->tableName);
        $this->dropTable('{{%edu_class_book}}');
    }
}
