<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story_test}}`.
 */
class m210720_124240_add_created_by_column_to_story_test_table extends Migration
{

    private $tableName = '{{%story_test}}';
    private $columnName = 'created_by';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->columnName, $this->integer()->notNull()->defaultValue(1));
        $this->addForeignKey(
            'fk-story_test-created_id',
            $this->tableName,
            $this->columnName,
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
        $this->dropForeignKey('fk-story_test-created_id', $this->tableName);
        $this->dropColumn($this->tableName, $this->columnName);
    }
}
