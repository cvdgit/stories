<?php


namespace frontend\models;


use common\models\Story;
use frontend\components\StorySorter as Sort;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StorySearch extends Model
{

    public $title;
    public $description;
    public $category_id;
    public $tag_id;

    public function rules()
    {
        return [
            [['title', 'description'], 'string'],
            [['category_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Story::findPublishedStories();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $sortParams = [
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                    'label' => 'названию истории',
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'дате создания',
                ],
                'updated_at' => [
                    'asc' => ['updated_at' => SORT_ASC],
                    'desc' => ['updated_at' => SORT_DESC],
                ],
            ],
        ];
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->category_id)) {
            $query->joinWith(['categories']);
        }

        if (!empty($this->tag_id)) {
            $query->joinWith(['tags']);
        }

        $query->andFilterWhere(['or',
            ['like', '{{%story}}.title', $this->title],
            ['like', '{{%story}}.description', $this->title],
        ]);
        $query->andFilterWhere([
            'category.id' => $this->category_id,
            'tag.id' => $this->tag_id,
        ]);

        return $dataProvider;
    }
}