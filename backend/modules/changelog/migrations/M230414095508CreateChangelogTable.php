<?php

namespace backend\modules\changelog\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%changelog}}`.
 */
class M230414095508CreateChangelogTable extends Migration
{
    private $tableName = '{{%changelog}}';

    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'slug' => $this->string()->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
