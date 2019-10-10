<?php

use yii\db\Migration;

/**
 * Class m191007_114810_create_story_audio_track
 */
class m191007_114810_create_story_audio_track extends Migration
{

    protected $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
    protected $tableName = '{{%story_audio_track}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'story_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger()->notNull(),
            'default' => $this->tinyInteger()->notNull()->defaultValue(1),
            'name' => $this->string()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $this->tableOptions);
        $this->addForeignKey(
            'fk_story_audio_track-story_id',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_story_audio_track-user_id',
            $this->tableName,
            'user_id',
            '{{%user}}',
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
        $this->dropTable($this->tableName);
    }

}
