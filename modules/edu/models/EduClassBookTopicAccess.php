<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\db\ActiveRecord;

/**
 * @property int $class_book_id [int(11)]
 * @property int $topic_id [int(11)]
 * @property int $created_at [int(11)]
 * @property bool $ord [tinyint(3)]
 */
class EduClassBookTopicAccess extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'edu_class_book_topic_access';
    }
}
