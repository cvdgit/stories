<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_repetition}}`.
 */
class m230213_062650_create_test_repetition_table extends Migration
{
    private $tableName = '{{%test_repetition}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'test_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'schedule_item_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'done' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createIndex('{{%idx-test_repetition-created_at}}', $this->tableName,'created_at');

        $this->createIndex('{{%idx-test_repetition-test_id}}', $this->tableName,'test_id');
        $this->addForeignKey(
            '{{%fk-test_repetition-test_id}}',
            $this->tableName,
            'test_id',
            '{{%story_test}}',
            'id',
            'CASCADE'
        );

        $this->createIndex('{{%idx-test_repetition-student_id}}', $this->tableName,'student_id');
        $this->addForeignKey(
            '{{%fk-test_repetition-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );

        $this->createIndex('{{%idx-test_repetition-schedule_item_id}}', $this->tableName,'schedule_item_id');
        $this->addForeignKey(
            '{{%fk-test_repetition-schedule_item_id}}',
            $this->tableName,
            'schedule_item_id',
            '{{%schedule_item}}',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-test_repetition-schedule_item_id}}', $this->tableName);
        $this->dropIndex('{{%idx-test_repetition-schedule_item_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-test_repetition-student_id}}', $this->tableName);
        $this->dropIndex('{{%idx-test_repetition-student_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-test_repetition-test_id}}', $this->tableName);
        $this->dropIndex('{{%idx-test_repetition-test_id}}', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
