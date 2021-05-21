<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%slide_video}}`.
 */
class m210519_122948_add_source_column_to_slide_video_table extends Migration
{

    private $tableName = '{{%slide_video}}';
    private $tableColumnName = 'source';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->tinyInteger()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->tableColumnName);
    }
}
