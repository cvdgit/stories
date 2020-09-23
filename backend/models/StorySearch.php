<?php

namespace backend\models;

use common\models\Story;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

class StorySearch extends Model
{

    public $id;
    public $title;
    public $user_id;
    public $category_id;
    public $created_at;
    public $updated_at;
    public $status;
    public $sub_access;

    public function rules()
    {
        return [
            [['title'], 'string'],
            [['id', 'user_id', 'status', 'sub_access'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            ['category_id', 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Story::find()->joinWith(['author', 'categories']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $sortParams = [
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'id' => [
                    'asc' => ['id' => SORT_ASC],
                    'desc' => ['id' => SORT_DESC],
                ],
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                ],
                'user_id' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                ],
                'updated_at' => [
                    'asc' => ['updated_at' => SORT_ASC],
                    'desc' => ['updated_at' => SORT_DESC],
                ],
                'status',
                'sub_access',
                'views_number',
            ],
        ];
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere([
            'story.id' => $this->id,
            'user.id' => $this->user_id,
            //'story_category.category_id' => $this->category_id,
            "DATE_FORMAT(FROM_UNIXTIME(story.created_at), '%d.%m.%Y')" => $this->created_at,
            "DATE_FORMAT(FROM_UNIXTIME(story.updated_at), '%d.%m.%Y')" => $this->updated_at,
            'story.status' => $this->status,
            'story.sub_access' => $this->sub_access,
        ]);
        if (!empty($this->category_id)) {
            $query->andFilterWhere(['in', 'category.id', explode(',', $this->category_id)]);
        }

        return $dataProvider;
    }

}