<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%site_section}}`.
 */
class m210819_134243_create_site_section_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%site_section}}', [
            'id' => $this->primaryKey(),
            'alias' => $this->string()->notNull()->unique(),
            'category_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->string(),
            'keywords' => $this->string(),
            'h1' => $this->string()->notNull(),
            'visible' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey(
            'fk_site_section-category_id',
            '{{%site_section}}',
            'category_id',
            '{{%category}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->batchInsert('{{%site_section}}', ['alias', 'category_id', 'title', 'description', 'keywords', 'h1', 'visible'], [
            ['stories', 1, 'Истории для детей', 'Истории для детей', 'Истории для детей, wikids, сказки, истории', 'Истории для детей', 1],
            ['education', 1, 'education', 'education', 'education', 'education', 0],
            ['directum', 1, 'directum', 'directum', 'directum', 'directum', 0],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%site_section}}');
        $this->dropTable('{{%site_section}}');
    }
}
