<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'group' => $this->smallInteger()->notNull()->defaultValue(2),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->batchInsert(
            '{{%user}}',
            ['id', 'username', 'auth_key', 'password_hash', 'email', 'status', 'group', 'created_at'],
            [
                [
                    'id' => '1',
                    'username' => 'admin',
                    'auth_key' => '_Ka3HNBiwcaqWp7bnBkhSLAXkj-VIRL7',
                    'password_hash' => '$2y$13$AFWRCIRuyeKoOyFp1Hb22eCxse.NpCWbwyluNswNxgvCK7I0sLAQO',
                    'email' => 'story@centrvd.ru',
                    'status' => '10',
                    'group' => '1',
                    'created_at' => '1539959113',
                ],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
