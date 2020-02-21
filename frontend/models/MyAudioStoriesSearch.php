<?php


namespace frontend\models;


use common\models\StoryAudioTrack;
use common\models\User;
use frontend\components\StorySorter as Sort;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\db\Query;

class MyAudioStoriesSearch extends Model
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

        $query = (new Query())
            ->select(['DISTINCT {{%story_audio_track}}.story_id', 'story.*', '{{%user_story_history}}.percent AS history_percent'])
            ->from('{{%story_audio_track}}')
            ->innerJoin('{{%story}}', '{{%story_audio_track}}.story_id = {{%story}}.id')
            ->innerJoin('{{%user_story_history}}', '{{%story_audio_track}}.story_id = {{%user_story_history}}.story_id')
            ->andWhere('{{%story_audio_track}}.user_id = :user', [':user' => $user->id])
            ->andWhere('{{%story_audio_track}}.type = :type', [':type' => StoryAudioTrack::TYPE_USER])
            ->andWhere('{{%user_story_history}}.user_id = :user', [':user' => $user->id]);

        $query->andFilterWhere(['or',
            ['like', '{{%story}}.title', $this->title],
            ['like', '{{%story}}.description', $this->title],
        ]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count('DISTINCT {{%story_audio_track}}.story_id'),
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $sortParams = [
            'defaultOrder' => ['published_at' => SORT_DESC],
            'attributes' => [
                'published_at' => [
                    'asc' => ['published_at' => SORT_ASC],
                    'desc' => ['published_at' => SORT_DESC],
                    'label' => 'дате публикации',
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