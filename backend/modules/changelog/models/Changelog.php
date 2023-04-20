<?php

declare(strict_types=1);

namespace backend\modules\changelog\models;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property string $slug [varchar(255)]
 * @property string $text
 * @property int $status [tinyint(3)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 *
 * @property ChangelogTag[] $changelogTags
 * @property Tag[] $tags
 */
class Changelog extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'changelog';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
            ],
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'changelogTags',
                ],
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'slug' => 'Alias',
            'text' => 'Текст',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    public static function create(string $title, string $text, int $createdAt): self
    {
        $model = new self();
        $model->title = $title;
        $model->text = $text;
        $model->created_at = $createdAt;
        $model->updated_at = $createdAt;
        return $model;
    }

    public function updateChangelog(string $title, string $text, int $status, int $updatedAt): void
    {
        $this->title = $title;
        $this->text = $text;
        $this->status = $status;
        $this->updated_at = $updatedAt;
    }

    public function getChangelogTags(): ActiveQuery
    {
        return $this->hasMany(ChangelogTag::class, ['changelog_id' => 'id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('changelog_tag', ['changelog_id' => 'id']);
    }

    public function updateTags(array $tags): void
    {
        $this->changelogTags = array_map(function($tagId) {
            $model = ChangelogTag::find()->where(['changelog_id' => $this->id, 'tag_id' => $tagId])->one();
            if ($model === null) {
                $model = new ChangelogTag(['changelog_id' => $this->id, 'tag_id' => $tagId]);
            }
            return $model;
        }, $tags);
    }
}
