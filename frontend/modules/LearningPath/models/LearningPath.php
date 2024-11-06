<?php

declare(strict_types=1);

namespace frontend\modules\LearningPath\models;

use yii\db\ActiveRecord;

/**
 * @property string $uuid [varchar(36)]
 * @property string $name [varchar(255)]
 * @property array $payload [json]
 * @property int $user_id [int(11)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 */
class LearningPath extends ActiveRecord {}
