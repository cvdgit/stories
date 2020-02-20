<?php

use common\models\Story;
use yii\db\Migration;

/**
 * Handles adding columns to table `{{%story}}`.
 */
class m200220_090659_add_published_at_column_to_story_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%story}}', 'published_at', $this->integer()->null());

        $command = Yii::$app->db->createCommand();
        $command->update('{{%story}}', ['published_at' => new \yii\db\Expression('created_at')], 'status = :status', [':status' => Story::STATUS_PUBLISHED]);
        $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%story}}', 'published_at');
    }

}
