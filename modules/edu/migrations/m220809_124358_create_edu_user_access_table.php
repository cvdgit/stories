<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_user_access}}`.
 */
class m220809_124358_create_edu_user_access_table extends Migration
{

    private $tableName = '{{%edu_user_access}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%idx-edu_user_access-user_id}}',
            $this->tableName,
            'user_id',
            true
        );

        $this->addForeignKey(
            '{{%fk-edu_user_access-user_id}}',
            $this->tableName,
            'user_id',
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
        $this->dropForeignKey('{{%fk-edu_user_access-user_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_user_access-user_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
