<?php

namespace modules\edu\migrations;

use yii\db\Migration;
use yii\db\Query;
use modules\edu\components\StudentLoginGenerator;

/**
 * Handles the creation of table `{{%student_login}}`.
 */
class m220728_121046_create_student_login_table extends Migration
{

    private $tableName = '{{%student_login}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'student_id' => $this->integer()->notNull(),
            'username' => $this->string(50)->notNull(),
            'password' => $this->string(50)->notNull(),
            'PRIMARY KEY(student_id, username, password)'
        ]);

        $this->addForeignKey(
            '{{%fk-student_login-student_id}}',
            $this->tableName,
            'student_id',
            '{{%user_student}}',
            'id',
            'CASCADE'
        );

        $rows = (new Query())
            ->select('id')
            ->from('user_student')
            ->where('status = 0')
            ->all();
        $command = $this->db->createCommand();
        foreach ($rows as $row) {
            $command->insert($this->tableName, [
                'student_id' => $row['id'],
                'username' => StudentLoginGenerator::generateLogin(),
                'password' => StudentLoginGenerator::generatePassword(),
            ])->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-student_login-student_id}}', $this->tableName);
        $this->dropTable('{{%student_login}}');
    }
}
