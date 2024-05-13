<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%game_deploy}}`.
 */
class M240511175555CreateGameDeployTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%game_deploy}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull(),
            'folder' => $this->string(36)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%game_deploy}}');
    }
}
