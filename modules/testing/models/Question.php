<?php

declare(strict_types=1);

namespace modules\testing\models;

use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $story_test_id [int(11)]
 * @property string $name [varchar(512)]
 * @property int $order [smallint(6)]
 * @property int $type [tinyint(3)]
 * @property int $mix_answers [tinyint(3)]
 * @property string $image [varchar(255)]
 * @property string $regions
 * @property string $hint [varchar(255)]
 * @property int $audio_file_id [int(11)]
 * @property int $sort_view [tinyint(3)]
 * @property string $incorrect_description
 */
class Question extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'story_test_question';
    }
}
