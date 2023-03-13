<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%image_slide_block}}`.
 */
class m210701_080317_add_deleted_column_to_image_slide_block_table extends Migration
{

    private $tableName = '{{%image_slide_block}}';
    private $columnName = 'deleted';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
