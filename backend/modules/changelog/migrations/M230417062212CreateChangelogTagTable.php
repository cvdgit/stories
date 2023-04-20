<?php

namespace backend\modules\changelog\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%changelog_tag}}`.
 */
class M230417062212CreateChangelogTagTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%changelog_tag}}', [
            'changelog_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'PRIMARY KEY(changelog_id, tag_id)',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%changelog_tag}}');
    }
}
