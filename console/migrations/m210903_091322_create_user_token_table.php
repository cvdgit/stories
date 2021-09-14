<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_token}}`.
 */
class m210903_091322_create_user_token_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%user_token}}', [
            'token' => $this->string(50),
            'user_id' => $this->integer(),
            'expired_at' => $this->integer()->notNull(),
            'PRIMARY KEY(token)'
        ]);

        $this->createIndex(
            '{{%idx-user_token-user_id}}',
            '{{%user_token}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-user_token-user_id}}',
            '{{%user_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-user_token-user_id}}', '{{%user_token}}');
        $this->dropIndex('{{%idx-user_token-user_id}}', '{{%user_token}}');
        $this->dropTable('{{%user_token}}');
    }
}
