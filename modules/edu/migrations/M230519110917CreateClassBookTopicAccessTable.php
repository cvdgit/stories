<?php

namespace modules\edu\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%class_book_topic_access}}`.
 */
class M230519110917CreateClassBookTopicAccessTable extends Migration
{
    private $tableName = '{{%edu_class_book_topic_access}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'class_book_id' => $this->integer()->notNull(),
            'class_program_id' => $this->integer()->notNull(),
            'topic_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'ord' => $this->tinyInteger()->notNull()->defaultValue(0),
            'PRIMARY KEY(class_book_id, class_program_id, topic_id)',
        ]);

        $this->addForeignKey(
            '{{%fk-class_book_topic_access-class_book_id}}',
            $this->tableName,
            'class_book_id',
            '{{%edu_class_book}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-class_book_topic_access-class_program_id}}',
            $this->tableName,
            'class_program_id',
            '{{%edu_class_program}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-class_book_topic_access-topic_id}}',
            $this->tableName,
            'topic_id',
            '{{%edu_topic}}',
            'id',
            'CASCADE'
        );

        \Yii::$app->db->createCommand(<<<SQL
INSERT INTO `edu_class_book_topic_access` (class_book_id, class_program_id, topic_id, created_at)
SELECT `cb`.id, `cp`.id, `t`.id, UNIX_TIMESTAMP()
FROM `edu_class_book` `cb`
  INNER JOIN `edu_class_book_program` `cbp` ON `cbp`.class_book_id = `cb`.id
  INNER JOIN `edu_class_program` `cp` ON `cbp`.class_program_id = `cp`.id
  INNER JOIN `edu_topic` `t` ON `cp`.id = `t`.class_program_id
SQL
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-class_book_topic_access-topic_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-class_book_topic_access-class_program_id}}', $this->tableName);
        $this->dropForeignKey('{{%fk-class_book_topic_access-class_book_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
