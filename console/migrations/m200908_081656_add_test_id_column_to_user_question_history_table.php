<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_question_history}}`.
 */
class m200908_081656_add_test_id_column_to_user_question_history_table extends Migration
{

    private $tableName = '{{%user_question_history}}';
    private $tableColumnName = 'test_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, $this->tableColumnName, $this->integer()->notNull());
        $command = Yii::$app->db->createCommand('UPDATE user_question_history t 
                                                     SET t.test_id = (SELECT t2.id FROM story_test t2 WHERE t2.question_list_id = t.question_topic_id)');
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
