<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_topic}}`.
 */
class m220802_090448_create_edu_topic_table extends Migration
{

    private $tableName = '{{%edu_topic}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'class_program_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'order' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            '{{%idx-edu_topic-class_program_id}}',
            $this->tableName,
            'class_program_id'
        );

        $this->addForeignKey(
            '{{%fk-edu_topic-class_program_id}}',
            $this->tableName,
            'class_program_id',
            '{{%edu_class_program}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-edu_topic-class_program_id}}', $this->tableName);
        $this->dropIndex('{{%idx-edu_topic-class_program_id}}', $this->tableName);
        $this->dropTable('{{%edu_topic}}');
    }
}
