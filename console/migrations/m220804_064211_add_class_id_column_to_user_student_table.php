<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_student}}`.
 */
class m220804_064211_add_class_id_column_to_user_student_table extends Migration
{

    private $tableName = '{{%user_student}}';
    private $columnName = 'class_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->integer()->null());
        $this->createIndex(
            '{{%idx-user_student-class_id}}',
            $this->tableName,
            $this->columnName
        );
        $this->addForeignKey(
            '{{%fk-user_student-class_id}}',
            $this->tableName,
            $this->columnName,
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
        $this->dropForeignKey('{{%fk-user_student-class_id}}', $this->tableName);
        $this->dropIndex('{{%idx-user_student-class_id}}', $this->tableName);
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
