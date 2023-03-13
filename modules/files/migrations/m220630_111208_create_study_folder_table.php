<?php

namespace modules\files\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_folder}}`.
 */
class m220630_111208_create_study_folder_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_folder}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'title' => $this->string(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'visible' => $this->tinyInteger()->notNull()->defaultValue(1),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%study_folder}}');
    }
}
