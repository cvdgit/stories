<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%game_data}}`.
 */
class M240403120320CreateGameDataTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%game_data}}', [
            'user_id INT NOT NULL',
            'data JSON NOT NULL',
        ]);

        $this->createIndex("idx-game_data-user_id", "{{%game_data}}", "user_id", true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex("idx-game_data-user_id", "{{%game_data}}");
        $this->dropTable('{{%game_data}}');
    }
}
