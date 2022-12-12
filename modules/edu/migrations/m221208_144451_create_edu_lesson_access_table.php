<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_lesson_access}}`.
 */
class m221208_144451_create_edu_lesson_access_table extends Migration
{
    private $tableName = '{{%edu_lesson_access}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'class_program_id' => $this->integer()->notNull(),
            'lesson_id' => $this->integer()->notNull(),
            'PRIMARY KEY(class_program_id, lesson_id)',
        ]);
        $this->addForeignKey(
            '{{%fk-edu_lesson_access-class_program_id}}',
            $this->tableName,
            'class_program_id',
            '{{%edu_class_program}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            '{{%fk-edu_lesson_access-lesson_id}}',
            $this->tableName,
            'lesson_id',
            '{{%edu_lesson}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_lesson_access-lesson_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_lesson_access-class_program_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
