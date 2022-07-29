<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_student_session}}`.
 */
class m220728_090239_create_user_student_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_student_session}}', [
            'uid' => $this->string(36)->unique()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_student_session}}');
    }
}
