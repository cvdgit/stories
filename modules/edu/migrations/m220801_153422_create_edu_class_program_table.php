<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_class_program}}`.
 */
class m220801_153422_create_edu_class_program_table extends Migration
{

    private $tableName = '{{%edu_class_program}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'class_id' => $this->integer()->notNull(),
            'program_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%idx-edu_class_program-class_program}}',
            $this->tableName,
            ['class_id', 'program_id'],
            true
        );

        $this->addForeignKey(
            '{{%fk-edu_class_program-class_id}}',
            $this->tableName,
            'class_id',
            '{{%edu_class}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-edu_class_program-program_id}}',
            $this->tableName,
            'program_id',
            '{{%edu_program}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_class_program-program_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_class_program-class_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_class_program-class_program}}', $this->tableName);
        $this->dropTable('{{%edu_class_program}}');
    }
}
