<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_parent_invite}}`.
 */
class m221013_134923_create_edu_parent_invite_table extends Migration
{
    private $tableName = '{{%edu_parent_invite}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'code' => $this->string()->notNull(),
        ]);

        $this->createIndex('idx-edu_parent_invite-invite', $this->tableName, ['email', 'student_id'], true);

        $this->addForeignKey(
            '{{%fk-edu_parent_invite-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-edu_parent_invite-invite', $this->tableName);
        $this->dropForeignKey('{{%fk-edu_parent_invite-student_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
