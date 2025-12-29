<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_restart_log}}`.
 */
class M251229094118CreateTestRestartLogTable extends Migration
{
    private $tableName = '{{%test_restart_log}}';

    public function up(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->string(36)->notNull(),
            'test_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'PRIMARY KEY(id)',
        ]);
        $this->createIndex(
            '{{%idx-test_restart_log-unique}}',
            $this->tableName,
            ['test_id', 'student_id'],
        );
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-test_restart_log-unique}}', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
