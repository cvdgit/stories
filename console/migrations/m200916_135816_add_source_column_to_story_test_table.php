<?php

namespace console\migrations;

use common\models\test\SourceType;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m200916_135816_add_source_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $tableColumnName = 'source';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->tinyInteger()->notNull()->defaultValue(1));

        $command = \Yii::$app->db->createCommand();
        $command->update($this->tableName, ['source' => SourceType::NEO], 'remote = 1');
        $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, $this->tableColumnName);
    }

}
