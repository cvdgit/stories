<?php


namespace frontend\models;


use common\models\User;
use yii\base\Model;
use frontend\components\StorySorter as Sort;
use yii\data\SqlDataProvider;
use yii\db\Query;

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

    public function search($params): SqlDataProvider
    {

        $this->load($params);
        if (!$this->validate()) {
            return new SqlDataProvider();
        }

        $user = User::findModel($this->user_id);

        $query = new Query();
        $query
            ->from('{{%user_story_history}}')
            ->innerJoin('{{%story}}', '{{%user_story_history}}.story_id = {{%story}}.id')
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

        return $dataProvider;
    }

}