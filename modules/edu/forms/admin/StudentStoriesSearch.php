<?php

declare(strict_types=1);

namespace modules\edu\forms\admin;

use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\SqlDataProvider;
use yii\db\Query;

class StudentStoriesSearch extends Model
{
    public $story_title;

    public function rules(): array
    {
        return [
            ['story_title', 'string'],
        ];
    }

    public function search(int $studentId, array $params): DataProviderInterface
    {
        $query = (new Query())
            ->select([
                'story_id' => 'story.id',
                'student_id' => 'story_student_progress.student_id',
                'story_title' => 'story.title',
                'updated_at' => 'story_student_progress.updated_at',
            ])
            ->from('story_student_progress')
            ->innerJoin('story', 'story_student_progress.story_id = story.id')
            ->where(['story_student_progress.student_id' => $studentId]);

        $this->load($params);
        if (!$this->validate()) {
            return new SqlDataProvider([
                'sql' => 'SELECT NULL LIMIT 0',
                'totalCount' => 0,
                'pagination' => false,
            ]);
        }

        $query->andFilterWhere(['like', 'story.title', $this->story_title]);

        return new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
                'attributes' => [
                    'story_title',
                    'story_student_progress.updated_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
