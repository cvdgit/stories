<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%insert_trigger_for_image_slide_block}}`.
 */
class m210715_095627_create_insert_trigger_for_image_slide_block_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $trigger = <<<SQL
CREATE TRIGGER `insert_in_story_story_slide_image` AFTER INSERT ON `image_slide_block` 
FOR EACH ROW
BEGIN
    DECLARE storyID integer;
    SET @storyID := (SELECT story_id FROM story_slide WHERE id = NEW.slide_id);
    IF NOT EXISTS(SELECT 1 FROM story_story_slide_image WHERE story_id = @storyID AND story_slide_image_id = NEW.image_id) THEN
        INSERT INTO story_story_slide_image SET story_slide_image_id = NEW.image_id, story_id = @storyID;
    END IF;
END
SQL;
        $command = Yii::$app->db->createCommand($trigger);
        $command->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $command = Yii::$app->db->createCommand('DROP TRIGGER IF EXISTS `insert_in_story_story_slide_image`');
        $command->execute();
    }
}
