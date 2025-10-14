<?php

declare(strict_types=1);

namespace backend\MentalMap;

use yii\db\ActiveRecord;

/**
 * @property string $mental_map_id
 * @property int $slide_id
 * @property string $block_id
 */
class MentalMapStorySlide extends ActiveRecord {}
