<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_statistics}}`.
 */
class m191111_073339_add_user_id_column_to_story_statistics_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_statistics}}', 'user_id', $this->integer()->null());
        $this->addForeignKey(
            'fk_story_statistics-user_id',
            '{{%story_statistics}}',
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
        $this->dropForeignKey('fk_story_statistics-user_id', '{{%story_statistics}}');
        $this->dropColumn('{{%story_statistics}}', 'user_id');
    }
}
