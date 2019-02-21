<?php

use yii\db\Migration;

/**
 * Class m190220_092934_create_table_story_statistics
 */
class m190220_092934_create_table_story_statistics extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        
        $this->createTable('{{%story_statistics}}', [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer()->notNull(),
            'slide_number' => $this->integer()->notNull(),
            'begin_time' => $this->integer()->notNull(),
            'end_time' => $this->integer()->notNull(),
            'chars' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'session' => $this->string(50)->notNull(),
        ], $tableOptions);
        
        $this->addForeignKey(
            'fk_storystatistics_story_id',
            '{{%story_statistics}}',
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
        echo "m190220_092934_create_table_story_statistics cannot be reverted.\n";
        $this->truncateTable('{{%story_statistics}} CASCADE');
        $this->dropForeignKey('fk_storystatistics_story_id', '{{%story_statistics}}');
        $this->dropTable('{{%story_statistics}}');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190220_092934_create_table_story_statistics cannot be reverted.\n";

        return false;
    }
    */
}
