<?php

namespace console\migrations;

use yii\db\Migration;

/**
 * Class M250516104121AlterMentalMapTable
 */
class M250516104121AlterMentalMapTable extends Migration
{
    private $tableName = '{{%mental_map}}';

    public function up(): void
    {
        $this->addColumn($this->tableName, 'map_type', $this->string()->notNull()->defaultValue('mental-map'));
        $this->createIndex('{{%idx-mental_map-map_type}}', $this->tableName, 'map_type');
        $this->addColumn($this->tableName, 'source_mental_map_id', $this->string(36)->null());
    }

    public function down(): void
    {
        $this->dropIndex('{{%idx-mental_map-map_type}}', $this->tableName);
        $this->dropColumn($this->tableName, 'map_type');
        $this->dropColumn($this->tableName, 'source_mental_map_id');
    }
}
