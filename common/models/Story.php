<?php

namespace common\models;

use DomainException;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use dosamigos\taggable\Taggable;
use common\helpers\Translit;

/**
 * This is the model class for table "story".
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $body
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property string $cover
 * @property int $status
 * @property int $category_id
 * @property int $sub_access
 * @property string $story_file
 * @property string $description
 * @property int $source_id
 * @property int $views_number
 * @property int $slides_number
 *
 * @property User $author
 * @property Tags $tags
 * @property Category $category
 * @property Comments $comments
 */

class Story extends ActiveRecord
{

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

    const SOURCE_SLIDESCOM = 1;
    const SOURCE_POWERPOINT = 2;

    public $source_dropbox = '';
    public $source_powerpoint = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%story}}';
    }

    public static function find()
    {
        return new StoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => Taggable::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'alias', 'user_id', 'category_id', 'source_id'], 'required'],
            [['body', 'cover', 'story_file', 'source_dropbox', 'source_powerpoint'], 'string'],
            [['created_at', 'updated_at', 'user_id', 'category_id', 'sub_access', 'source_id', 'views_number', 'slides_number'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            [['tagNames'], 'safe'],
            [['description'], 'string', 'max' => 1024],
            ['source_id', 'default', 'value' => self::SOURCE_POWERPOINT],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'title' => 'Название истории',
            'alias' => 'Alias',
            'body' => 'Body',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'user_id' => 'Автор',
            'status' => 'Статус',
            'tagNames' => 'Тэги',
            'category_id' => 'Категория',
            'sub_access' => 'По подписке',
            'cover' => 'Обложка',
            'story_file' => 'Файл PowerPoint',
            'description' => 'Краткое описание',
            'source_id' => 'Источник',
            'source_dropbox' => 'Имя истории в Slides.com',
            'source_powerpoint' => 'Файл PowerPoint (pptx)',
            'views_number' => 'Просмотров',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('{{%story_tag}}', ['story_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['story_id' => 'id']);
    }

    public static function findStories()
    {
        return self::find();
    }

    public static function findPublishedStories()
    {
        return self::find()->published();
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_PUBLISHED => 'Опубликован',
        ];
    }

    public function getStatusText()
    {
        $arr = self::getStatusArray();
        return $arr[$this->status];
    }

    public static function getSubAccessArray()
    {
        return [
            1 => 'Да',
            0 => 'Нет',
        ];
    }    

    public function getSubAccessText()
    {
        $arr = self::getSubAccessArray();
        return $arr[$this->sub_access];
    }

    public static function findLastPublishedStories()
    {
        return self::find()->published()->lastStories()->all();
    }

    public static function getSourceList()
    {
        return [
            self::SOURCE_SLIDESCOM => 'Slides.com (Dropbox)',
            self::SOURCE_POWERPOINT => 'Файл PowerPoint (PPTX)',
        ];
    }

    public function saveBody($body)
    {
        $this->body = $body;
        return $this->save(false, ['body']);
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->alias)) {
                $this->alias = Translit::translit($this->title);
            }
            return true;
        }
        return false;
    }

    public function bySubscription()
    {
        return !empty($this->sub_access);
    }

    public static function findStory($condition)
    {
        return static::findByCondition($condition)->published()->one();
    }

    public static function forSlider($number = 4)
    {
        return static::find()->published()->withCover()->byRand()->limit($number)->all();
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('История не найдена');
    }

    public static function findModelByAlias($alias): self
    {
        if (($model = self::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new DomainException('История не найдена');
    }
}
