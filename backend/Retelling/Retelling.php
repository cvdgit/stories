<?php

declare(strict_types=1);

namespace backend\Retelling;

use DomainException;
use Ramsey\Uuid\UuidInterface;
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
class Retelling extends ActiveRecord
{
    public static function copyRetelling(UuidInterface $id, UuidInterface $newId, string $newName, int $userId): self
    {
        $retelling = self::findOne($id->toString());
        if ($retelling === null) {
            throw new DomainException('Retelling not found');
        }
        $newRetelling = new self();
        $newRetelling->id = $newId->toString();
        $newRetelling->slide_id = $retelling->slide_id;
        $newRetelling->questions = $retelling->questions;
        $newRetelling->with_questions = $retelling->with_questions;
        $newRetelling->name = $newName;
        $newRetelling->created_by = $userId;
        $newRetelling->created_at = time();
        $newRetelling->updated_at = time();
        return $newRetelling;
    }

    public static function create(UuidInterface $id, int $slideId, string $name, string $questions, bool $withQuestions, int $userId): self
    {
        $model = new self();
        $model->id = $id->toString();
        $model->slide_id = $slideId;
        $model->name = $name;
        $model->questions = $questions;
        $model->with_questions = $withQuestions ? 1 : 0;
        $model->created_by = $userId;
        $model->created_at = time();
        $model->updated_at = time();
        return $model;
    }
}
