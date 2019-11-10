<?php

use yii\db\Migration;

/**
 * Class m191108_135623_add_slide_id_column_to_story_statistics
 */
class m191108_135623_add_slide_id_column_to_story_statistics extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story_statistics}}', 'slide_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk_story_statistics-slide_id',
            '{{%story_statistics}}',
            'slide_id',
            '{{%story_slide}}',
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
        $this->dropForeignKey('fk_story_statistics-slide_id', '{{%story_statistics}}');
        $this->dropColumn('{{%story_statistics}}', 'slide_id');
    }

}
