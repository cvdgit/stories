<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fragment_list_tag}}`.
 */
class m230106_085205_create_fragment_list_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%fragment_list_tag}}', [
            'fragment_list_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'PRIMARY KEY(fragment_list_id, tag_id)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%fragment_list_tag}}');
    }
}
