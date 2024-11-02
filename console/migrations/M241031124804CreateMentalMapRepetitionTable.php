<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map_repetition}}`.
 */
class M241031124804CreateMentalMapRepetitionTable extends Migration
{
    private $tableName = '{{%mental_map_repetition}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'mental_map_id' => $this->string(36)->notNull(),
            'student_id' => $this->integer()->notNull(),
            'schedule_item_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'done' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createIndex('{{%idx-mental_map_repetition-created_at}}', $this->tableName, 'created_at');

        $this->createIndex('{{%idx-mental_map_repetition-mental_map_id}}', $this->tableName, 'mental_map_id');
        $this->addForeignKey(
            '{{%fk-mental_map_repetition-mental_map_id}}',
            $this->tableName,
            'mental_map_id',
            '{{%mental_map}}',
            'uuid',
            'CASCADE',
        );

        $this->createIndex('{{%idx-mental_map_repetition-student_id}}', $this->tableName, 'student_id');
        $this->addForeignKey(
            '{{%fk-mental_map_repetition-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-mental_map_repetition-schedule_item_id}}', $this->tableName, 'schedule_item_id');
        $this->addForeignKey(
            '{{%fk-mental_map_repetition-schedule_item_id}}',
            $this->tableName,
            'schedule_item_id',
            '{{%schedule_item}}',
            'id',
            'RESTRICT',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-mental_map_repetition-schedule_item_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_repetition-schedule_item_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-mental_map_repetition-student_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_repetition-student_id}}', $this->tableName);

        $this->dropForeignKey('{{%fk-mental_map_repetition-mental_map_id}}', $this->tableName);
        $this->dropIndex('{{%idx-mental_map_repetition-mental_map_id}}', $this->tableName);

        $this->dropTable($this->tableName);
    }
}
