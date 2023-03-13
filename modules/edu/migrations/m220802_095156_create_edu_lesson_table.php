<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_lesson}}`.
 */
class m220802_095156_create_edu_lesson_table extends Migration
{

    private $tableName = '{{%edu_lesson}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'topic_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            '{{%idx-edu_lesson-topic_id}}',
            $this->tableName,
            'topic_id'
        );

        $this->addForeignKey(
            '{{%fk-edu_lesson-topic_id}}',
            $this->tableName,
            'topic_id',
            '{{%edu_topic}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_lesson-topic_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_lesson-topic_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
