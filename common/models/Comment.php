<?php

namespace common\models;

use DomainException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $user_id
 * @property int $story_id
 * @property string $body
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $parent_id
 *
 * @property Story $story
 * @property User $user
 */
class Comment extends ActiveRecord
{

    /**
     * @var null|array|ActiveRecord[] comment children
     */
    protected $children;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'story_id', 'body'], 'required'],
            [['user_id', 'story_id', 'status', 'parent_id'], 'integer'],
            ['body', 'string'],
            ['body', 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            ['body', 'filter', 'filter' => 'strip_tags'],
            ['story_id', 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            ['user_id', 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Автор',
            'story_id' => 'История',
            'body' => 'Комментарий',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата редактирования',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    public static function getStoryComments($storyID): ActiveDataProvider
    {
        $query = self::find()->joinWith(['user.profile.profilePhoto']);
        $query->andFilterWhere(['story_id' => $storyID]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => ['defaultOrder' => ['created_at' => SORT_ASC]],
        ]);
        return $dataProvider;
    }

    public static function findModel(int $id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Комментарий не найден');
    }

    /**
     * Get comments tree.
     *
     * @param int $storyID
     * @return array|ActiveRecord[]
     */
    public static function getTree(int $storyID)
    {
        $query = static::find()
            ->alias('c')
            ->andWhere([
                'c.story_id' => $storyID,
            ])
            ->orderBy(['c.created_at' => SORT_ASC])
            ->with(['user.profile.profilePhoto']);
        $models = $query->all();
        if (!empty($models)) {
            $models = static::buildTree($models);
        }
        return $models;
    }

    /**
     * Build comments tree.
     *
     * @param array $data comments list
     * @param int $rootID
     *
     * @return array|ActiveRecord[]
     */
    protected static function buildTree(&$data, $rootID = 0)
    {
        $tree = [];
        foreach ($data as $id => $node) {
            if ($node->parent_id === $rootID) {
                unset($data[$id]);
                $node->children = self::buildTree($data, $node->id);
                $tree[] = $node;
            }
        }
        return $tree;
    }

    /**
     * @return array|null|ActiveRecord[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param $value
     */
    public function setChildren($value)
    {
        $this->children = $value;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Get comment ArrayDataProvider
     *
     * @param int $storyID
     * @return ArrayDataProvider
     */
    public static function getCommentDataProvider(int $storyID)
    {
        $dataProvider = new ArrayDataProvider([
            'pagination' => [
                'pageSize' => false,
            ],
        ]);
        $dataProvider->allModels = self::getTree($storyID);
        return $dataProvider;
    }

    public function getLeadCommentAuthorID()
    {
        return (new Query())->from(self::tableName())
            ->where('id = :id', [':id' => $this->parent_id])
            ->select('user_id')
            ->scalar();
    }

    public function isMyReply(int $replyUserID)
    {
        return (int)$this->getLeadCommentAuthorID() === $replyUserID;
    }

}
