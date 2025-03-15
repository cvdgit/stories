<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%retelling}}`.
 */
class M250310074247CreateRetellingTable extends Migration
{
    private $tableName = '{{%retelling}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'slide_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'questions' => $this->text()->null(),
            'with_questions' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'PRIMARY KEY(id)',
        ]);

        $this->createIndex('{{%idx-retelling-slide_id}}', $this->tableName, 'slide_id');
        $this->addForeignKey(
            '{{%fk-retelling-slide_id}}',
            $this->tableName,
            'slide_id',
            '{{%story_slide}}',
            'id',
            'CASCADE',
        );

        $this->createIndex('{{%idx-retelling-created_by}}', $this->tableName, 'created_by');
        $this->addForeignKey(
            '{{%fk-retelling-created_by}}',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
        );
    }

    public function down(): void
    {
        $this->dropForeignKey('{{%fk-retelling-created_by}}', $this->tableName);
        $this->dropIndex('{{%idx-retelling-created_by}}', $this->tableName);
        $this->dropForeignKey('{{%fk-retelling-slide_id}}', $this->tableName);
        $this->dropIndex('{{%idx-retelling-slide_id}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
