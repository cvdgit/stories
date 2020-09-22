<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_word}}`.
 */
class m200916_082039_create_test_word_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%test_word}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'word_list_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull()->defaultValue(1),
        ]);
        $this->addForeignKey(
            'fk-test_word-word_list_id',
            '{{%test_word}}',
            'word_list_id',
            '{{%test_word_list}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%test_word}}');
    }
}
