<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m181031_140016_tag_tables
 */
class m181031_140016_tag_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%tag}}', [
            'id' => $this->primaryKey(),
            'frequency' => $this->integer()->notNull()->defaultValue(0),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);
         $this->createTable('{{%story_tag}}', [
            'story_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey('pk-story_tag', '{{%story_tag}}', ['story_id', 'tag_id']);
        $this->addForeignKey(
            'fk-story_tag-story_id',
            '{{%story_tag}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-story_tag-tag_id',
            '{{%story_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181031_140016_tag_tables cannot be reverted.\n";
        $this->truncateTable('{{%story_tag}}');
        $this->dropForeignKey('fk-story_tag-story_id', '{{%story_tag}}');
        $this->dropForeignKey('fk-story_tag-tag_id', '{{%story_tag}}');
        $this->dropTable('{{%story_tag}}');
        $this->dropTable('{{%tag}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_140016_tag_tables cannot be reverted.\n";

        return false;
    }
    */
}
