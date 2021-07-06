<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_slide_image}}`.
 */
class m210628_092243_add_filename_column_to_story_slide_image_table extends Migration
{

    private $tableName = '{{%story_slide_image}}';
    private $columnName = 'filename';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
