<?php

namespace console\migrations;

use yii\db\Migration;
use yii\db\Query;

/**
 * Handles the creation of table `{{%story_story_slide_image}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%story}}`
 * - `{{%story_slide_image}}`
 */
class m210715_085924_create_junction_table_for_story_and_story_slide_image_table extends Migration
{

    private $tableName = '{{%story_story_slide_image}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%story_story_slide_image}}', [
            'story_id' => $this->integer(),
            'story_slide_image_id' => $this->integer(),
            'PRIMARY KEY(story_id, story_slide_image_id)',
        ]);

        // creates index for column `story_id`
        $this->createIndex(
            '{{%idx-story_story_slide_image-story_id}}',
            '{{%story_story_slide_image}}',
            'story_id'
        );

        // add foreign key for table `{{%story}}`
        $this->addForeignKey(
            '{{%fk-story_story_slide_image-story_id}}',
            '{{%story_story_slide_image}}',
            'story_id',
            '{{%story}}',
            'id',
            'CASCADE'
        );

        // creates index for column `story_slide_image_id`
        $this->createIndex(
            '{{%idx-story_story_slide_image-story_slide_image_id}}',
            '{{%story_story_slide_image}}',
            'story_slide_image_id'
        );

        // add foreign key for table `{{%story_slide_image}}`
        $this->addForeignKey(
            '{{%fk-story_story_slide_image-story_slide_image_id}}',
            '{{%story_story_slide_image}}',
            'story_slide_image_id',
            '{{%story_slide_image}}',
            'id',
            'CASCADE'
        );

        $query = (new Query())
            ->select([
                "DISTINCT CONCAT({{%image_slide_block}}.image_id, '|', {{%story_slide}}.story_id)",
                '{{%image_slide_block}}.image_id',
                '{{%story_slide}}.story_id',
                ])
            ->from('{{%image_slide_block}}')
            ->innerJoin('{{%story_slide}}', '{{%story_slide}}.id = {{%image_slide_block}}.slide_id');
        $command = \Yii::$app->db->createCommand();
        foreach ($query->batch(500) as $batch) {
            $rows = [];
            foreach ($batch as $row) {
                $rows[] = [$row['story_id'], $row['image_id']];
            }
            $command->batchInsert($this->tableName, ['story_id', 'story_slide_image_id'], $rows)->execute();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%story}}`
        $this->dropForeignKey(
            '{{%fk-story_story_slide_image-story_id}}',
            '{{%story_story_slide_image}}'
        );

        // drops index for column `story_id`
        $this->dropIndex(
            '{{%idx-story_story_slide_image-story_id}}',
            '{{%story_story_slide_image}}'
        );

        // drops foreign key for table `{{%story_slide_image}}`
        $this->dropForeignKey(
            '{{%fk-story_story_slide_image-story_slide_image_id}}',
            '{{%story_story_slide_image}}'
        );

        // drops index for column `story_slide_image_id`
        $this->dropIndex(
            '{{%idx-story_story_slide_image-story_slide_image_id}}',
            '{{%story_story_slide_image}}'
        );

        $this->dropTable('{{%story_story_slide_image}}');
    }
}
