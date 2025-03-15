<?php

declare(strict_types=1);

namespace backend\Retelling;

use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property int $slide_id
 * @property string $name
 * @property string $questions
 * @property int $with_questions
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_at
 */
class Retelling extends ActiveRecord {}
