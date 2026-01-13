<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mental_map_restart_log}}`.
 */
class M260113090631CreateMentalMapRestartLogTable extends Migration
{
    private $tableName = '{{%mental_map_restart_log}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'mental_map_id' => $this->string(36)->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'PRIMARY KEY(id)',
        ]);
        $this->createIndex(
            '{{%idx-mental_map_restart_log-unique}}',
            $this->tableName,
            ['mental_map_id', 'student_id'],
        );
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-mental_map_restart_log-unique}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
