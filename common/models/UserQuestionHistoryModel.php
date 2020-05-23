<?php


namespace common\models;


use yii\base\Model;
use yii\db\Query;

class UserQuestionHistoryModel extends Model
{

    public $user_id;
    public $slide_id;
    public $question_topic_id;
    public $question_topic_name;
    public $entity_id;
    public $entity_name;
    public $relation_id;
    public $relation_name;
    public $correct_answer;

    public function __construct($userID, $config = [])
    {
        $this->user_id = $userID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['user_id', 'slide_id', 'question_topic_id', 'question_topic_name', 'entity_id', 'entity_name', 'relation_id', 'relation_name'], 'required'],
            [['user_id', 'slide_id', 'question_topic_id', 'entity_id', 'relation_id', 'correct_answer'], 'integer'],
            [['question_topic_name', 'entity_name', 'relation_name'], 'string', 'max' => 255],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function createUserQuestionHistory()
    {
        if (!$this->validate()) {
            throw new \DomainException('User question history data is not valid');
        }
        $model = UserQuestionHistory::create(
            $this->user_id,
            $this->slide_id,
            $this->question_topic_id,
            $this->question_topic_name,
            $this->entity_id,
            $this->entity_name,
            $this->relation_id,
            $this->relation_name,
            $this->correct_answer
        );
        $model->save();
    }

    public function getUserQuestionHistory()
    {
        $topicID = 1;
        return (new Query())
            ->select(['entity_id', 'relation_id'])
            ->distinct(true)
            ->from(UserQuestionHistory::tableName())
            ->where('user_id = :user', [':user' => $this->user_id])
            ->andWhere('question_topic_id = :topic', [':topic' => $topicID])
            ->andWhere('correct_answer = 1')
            ->all();
    }

}