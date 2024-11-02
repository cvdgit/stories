<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $name
 * @property array $payload
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 */
class MentalMap extends ActiveRecord {}
