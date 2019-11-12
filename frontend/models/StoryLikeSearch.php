<?php


namespace frontend\models;


use common\models\User;
use frontend\components\StorySorter as Sort;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;

class StoryLikeSearch extends Model
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

    public function search($params): SqlDataProvider
    {

        $this->load($params);
        if (!$this->validate()) {
            return new SqlDataProvider();
        }

        $user = User::findModel($this->user_id);

        $query = new Query();
        $query
            ->select(['story.*', '{{%user_story_history}}.percent AS history_percent'])
            ->from('{{%story_like}}')
            ->innerJoin('{{%story}}', '{{%story_like}}.story_id = {{%story}}.id')
            ->innerJoin('{{%user_story_history}}', '{{%story_like}}.story_id = {{%user_story_history}}.story_id')
            ->andWhere('{{%story_like}}.user_id = :user', [':user' => $user->id])
            ->andWhere('{{%story_like}}.action = :action', [':action' => StoryLikeForm::LIKE])
            ->andWhere('{{%user_story_history}}.user_id = :user', [':user' => $user->id]);

        $query->andFilterWhere(['or',
            ['like', '{{%story}}.title', $this->title],
            ['like', '{{%story}}.description', $this->title],
        ]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $sortParams = [
            'defaultOrder' => ['created_at' => SORT_DESC],
            'attributes' => [
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'дате создания',
                ],
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                    'label' => 'названию истории',
                ],
            ],
        ];
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        return $dataProvider;
    }
}