<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%story_story_test}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%story}}`
 * - `{{%story_test}}`
 */
class m201123_100007_create_junction_table_for_story_and_story_test_table extends Migration
{

    private $tableName = '{{%story_story_test}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'story_id' => $this->integer(),
            'test_id' => $this->integer(),
            'PRIMARY KEY(story_id, test_id)',
        ]);

        // creates index for column `story_id`
        $this->createIndex(
            '{{%idx-story_story_test-story_id}}',
            $this->tableName,
            'story_id'
        );

        // add foreign key for table `{{%story_slide}}`
        $this->addForeignKey(
            '{{%fk-story_story_test-story_id}}',
            $this->tableName,
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        // creates index for column `test_id`
        $this->createIndex(
            '{{%idx-story_story_test-test_id}}',
            $this->tableName,
            'test_id'
        );

        // add foreign key for table `{{%story_test}}`
        $this->addForeignKey(
            '{{%fk-story_story_test-test_id}}',
            $this->tableName,
            'test_id',
            '{{%story_test}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%story}}`
        $this->dropForeignKey(
            '{{%fk-story_story_test-story_id}}',
            $this->tableName
        );

        // drops index for column `story_id`
        $this->dropIndex(
            '{{%idx-story_story_test-story_id}}',
            $this->tableName
        );

        // drops foreign key for table `{{%story_test}}`
        $this->dropForeignKey(
            '{{%fk-story_story_test-test_id}}',
            $this->tableName
        );

        // drops index for column `test_id`
        $this->dropIndex(
            '{{%idx-story_story_test-test_id}}',
            $this->tableName
        );

        $this->dropTable($this->tableName);
    }
}
