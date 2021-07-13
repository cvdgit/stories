<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%related_tests}}`.
 */
class m210709_121239_create_related_tests_table extends Migration
{

    private $tableName = '{{%related_tests}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'test_id' => $this->integer()->notNull(),
            'related_test_id' => $this->integer()->notNull(),
            'PRIMARY KEY(test_id, related_test_id)',
        ]);
        $this->addForeignKey(
            'fk-related_tests-test_id',
            $this->tableName,
            'test_id',
            '{{%story_test}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-related_tests-related_test_id',
            $this->tableName,
            'related_test_id',
            '{{%story_test}}',
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
        $this->truncateTable($this->tableName);
        $this->dropForeignKey('fk-related_tests-test_id', $this->tableName);
        $this->dropForeignKey('fk-related_tests-related_test_id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
