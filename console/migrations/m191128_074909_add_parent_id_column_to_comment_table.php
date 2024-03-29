<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%comment}}`.
 */
class m191128_074909_add_parent_id_column_to_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%comment}}', 'parent_id', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%comment}}', 'parent_id');
    }
}
