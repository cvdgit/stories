<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%video_caption}}`.
 */
class M230627140618CreateVideoCaptionTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%video_caption}}', [
            'id' => $this->primaryKey(),
            'video_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'lang' => $this->string(32)->notNull(),
            'content' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-video_caption-video_id',
            '{{%video_caption}}',
            'video_id',
            '{{%slide_video}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-video_caption-video_id', '{{%video_caption}}');
        $this->dropTable('{{%video_caption}}');
    }
}
