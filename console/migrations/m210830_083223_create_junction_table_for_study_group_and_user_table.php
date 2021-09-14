<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_group_user}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%study_group}}`
 * - `{{%user}}`
 */
class m210830_083223_create_junction_table_for_study_group_and_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_group_user}}', [
            'study_group_id' => $this->integer(),
            'user_id' => $this->integer(),
            'PRIMARY KEY(study_group_id, user_id)',
        ]);

        // creates index for column `study_group_id`
        $this->createIndex(
            '{{%idx-study_group_user-study_group_id}}',
            '{{%study_group_user}}',
            'study_group_id'
        );

        // add foreign key for table `{{%study_group}}`
        $this->addForeignKey(
            '{{%fk-study_group_user-study_group_id}}',
            '{{%study_group_user}}',
            'study_group_id',
            '{{%study_group}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-study_group_user-user_id}}',
            '{{%study_group_user}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-study_group_user-user_id}}',
            '{{%study_group_user}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%study_group}}`
        $this->dropForeignKey(
            '{{%fk-study_group_user-study_group_id}}',
            '{{%study_group_user}}'
        );

        // drops index for column `study_group_id`
        $this->dropIndex(
            '{{%idx-study_group_user-study_group_id}}',
            '{{%study_group_user}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-study_group_user-user_id}}',
            '{{%study_group_user}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-study_group_user-user_id}}',
            '{{%study_group_user}}'
        );

        $this->dropTable('{{%study_group_user}}');
    }
}
