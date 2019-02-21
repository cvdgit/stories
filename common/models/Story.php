<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use dosamigos\taggable\Taggable;
use yii\db\Expression;

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
 * @property int $dropbox_sync_date
 * @property string $dropbox_story_filename
 * @property string $story_file
 * @property string $description
 * @property int $source_id
 * @property int $views_number
 *
 * @property User $author
 * @property Tags $tags
 * @property Category $category
 */
class Story extends \yii\db\ActiveRecord
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
            TimestampBehavior::className(),
            [
                'class' => Taggable::className(),
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
            [['created_at', 'updated_at', 'user_id', 'category_id', 'sub_access', 'dropbox_sync_date', 'source_id', 'views_number'], 'integer'],
            [['title', 'alias', 'dropbox_story_filename'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            [['tagNames'], 'safe'],
            [['description'], 'string', 'max' => 1024],
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
            'dropbox_sync_date' => 'Синхронизация с Dropbox',
            'dropbox_story_filename' => 'Файл в Dropbox',
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
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable('{{%story_tag}}', ['story_id' => 'id']);
    }

    public static function findStories()
    {
        return self::find();
    }

    public static function findPublishedStories()
    {
        return self::find()->published()->bySubAccess();
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

    public function isDropboxSync()
    {
        return !empty($this->dropbox_sync_date);
    }

    public static function findLastPublishedStories()
    {
        return self::find()->published()->lastStories();
    }

    public static function getSourceList()
    {
        return [
            self::SOURCE_SLIDESCOM => 'Slides.com (Dropbox)',
            self::SOURCE_POWERPOINT => 'Файл PowerPoint (PPTX)',
        ];
    }

}
