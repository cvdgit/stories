<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_notification}}`.
 */
class m200611_071736_create_user_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_notification}}', [
            'notification_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'read' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addPrimaryKey('pk-user_notification', '{{%user_notification}}', ['notification_id', 'user_id']);
        $this->addForeignKey(
            'fk-user_notification-notification_id',
            '{{%user_notification}}',
            'notification_id',
            '{{%notification}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-user_notification-user_id',
            '{{%user_notification}}',
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
        $this->truncateTable('{{%user_notification}}');
        $this->dropForeignKey('fk-user_notification-notification_id', '{{%user_notification}}');
        $this->dropForeignKey('fk-user_notification-user_id', '{{%user_notification}}');
        $this->dropTable('{{%user_notification}}');
    }
}
