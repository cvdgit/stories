<?php


namespace frontend\models;


use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\components\StorySorter as Sort;

class UserStorySearch extends Model
{

    public $user_id;
    public $title;

    public function __construct(int $userID, $config = [])
    {
        $this->user_id = $userID;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['title', 'string'],
            ['user_id', 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $user = User::findModel($this->user_id);
        $query = $user->getStoryHistory()->published();
        $query->joinWith('historyUser');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $sortParams = [
            'defaultOrder' => ['user_story_history.updated_at' => SORT_DESC],
            'attributes' => [
                'user_story_history.updated_at' => [
                    'asc' => ['user_story_history.updated_at' => SORT_ASC],
                    'desc' => ['user_story_history.updated_at' => SORT_DESC],
                    'label' => 'дате добавления в историю',
                ],
            ],
        ];
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['or',
            ['like', '{{%story}}.title', $this->title],
            ['like', '{{%story}}.description', $this->title],
        ]);

        return $dataProvider;
    }

}