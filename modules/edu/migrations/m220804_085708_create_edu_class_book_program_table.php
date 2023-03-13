<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class_book_program}}`.
 */
class m220804_085708_create_edu_class_book_program_table extends Migration
{

    private $tableName = '{{%edu_class_book_program}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'class_book_id' => $this->integer()->notNull(),
            'class_program_id' => $this->integer()->notNull(),
            'PRIMARY KEY(class_book_id, class_program_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-edu_class_book_program-class_book_id}}',
            $this->tableName,
            'class_book_id',
            '{{%edu_class_book}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-edu_class_book_program-class_program_id}}',
            $this->tableName,
            'class_program_id',
            '{{%edu_class_program}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_class_book_program-class_program_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_class_book_program-class_book_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
