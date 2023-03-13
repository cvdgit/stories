<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class m210520_130710_update__slide_video_table
 */
class m210520_130710_update__slide_video_table extends Migration
{

    private $tableName = '{{%slide_video}}';
    private $columnName = 'video_id';
    private $indexName = 'video_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex($this->indexName, $this->tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex($this->indexName, $this->tableName, $this->columnName, true);
    }

}
