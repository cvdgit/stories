<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_playlist}}`.
 */
class m191112_065529_create_story_playlist_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%story_playlist}}', [
            'story_id' => $this->integer()->notNull(),
            'playlist_id' => $this->integer()->notNull(),
            'order' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-story_playlist', '{{%story_playlist}}', ['story_id', 'playlist_id']);

        $this->addForeignKey(
            'fk-story_playlist-story_id',
            '{{%story_playlist}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-story_playlist-playlist_id',
            '{{%story_playlist}}',
            'playlist_id',
            '{{%playlist}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%story_playlist}}');
        $this->dropForeignKey('fk-story_playlist-story_id', '{{%story_playlist}}');
        $this->dropForeignKey('fk-story_playlist-playlist_id', '{{%story_playlist}}');
        $this->dropTable('{{%story_playlist}}');
    }
}
