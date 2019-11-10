<?php

use yii\db\Migration;

/**
 * Class m191110_094010_create_table_story_readonly_statistics
 */
class m191110_094010_create_table_story_readonly_statistics extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%story_readonly_statistics}}', [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_story_readonly_statistics-story_id',
            '{{%story_readonly_statistics}}',
            'story_id',
            '{{%story}}',
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
        $this->truncateTable('{{%story_readonly_statistics}} CASCADE');
        $this->dropForeignKey('fk_story_readonly_statistics-story_id', '{{%story_readonly_statistics}}');
        $this->dropTable('{{%story_readonly_statistics}}');
    }

}
