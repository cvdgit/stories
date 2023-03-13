<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%category}}`.
 */
class m190828_112115_add_sort_field_column_to_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%category}}', 'sort_field', $this->string(50)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%category}}', 'sort_field');
    }
}
