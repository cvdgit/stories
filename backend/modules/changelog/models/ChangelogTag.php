<?php

declare(strict_types=1);

namespace backend\modules\changelog\models;

use yii\db\ActiveRecord;

/**
 * @property int $changelog_id [int(11)]
 * @property int $tag_id [int(11)]
 */
class ChangelogTag extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'changelog_tag';
    }
}
