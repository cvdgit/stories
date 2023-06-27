<?php

namespace console\migrations;

use Ramsey\Uuid\Uuid;
use yii\db\Migration;
use yii\db\Query;

/**
 * Handles adding columns to table `{{%slide_video}}`.
 */
class M230627140617AddUuidColumnToSlideVideoTable extends Migration
{
    private $tableName = '{{%slide_video}}';
    private $columnName = 'uuid';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->string(36)->notNull());

        $rows = (new Query())
            ->select('*')
            ->from('slide_video')
            ->all();
        foreach ($rows as $row) {
            \Yii::$app->db->createCommand()
                ->update('slide_video', ['uuid' => Uuid::uuid4()->toString()], ['id' => $row['id']])
                ->execute();
        }

        $this->createIndex('idx-slide_video-uuid', $this->tableName, 'uuid', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
