<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use dosamigos\taggable\Taggable;
use yii\helpers\Html;

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
 *
 * @property User $author
 * @property Tags $tags
 * @property Category $category
 */
class Story extends \yii\db\ActiveRecord
{

    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;

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
            [['title', 'alias', 'user_id', 'category_id'], 'required'],
            [['body', 'cover'], 'string'],
            [['created_at', 'updated_at', 'user_id', 'category_id'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            [['tagNames'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'title' => 'Заголовок',
            'alias' => 'Alias',
            'body' => 'Body',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'user_id' => 'Автор',
            'status' => 'Статус',
            'tagNames' => 'Тэги',
            'category_id' => 'Категория',
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

    public function initStory()
    {
        $localFolder = $this->getImagesFolderPath();
        if (!file_exists($localFolder))
        {
            mkdir($localFolder, 0777);
        }
    }

    public function getCoverPath()
    {
        return $this->getImagesFolderPath(true) . '/' . $this->cover;
    }

    public function exportSlideBodyFromDropBox()
    {
        $dropboxPath = Yii::$app->params['dropboxSlidesPath'] . $this->alias . '.html';
        $html = Yii::$app->dropbox->read($dropboxPath);
        $document = \phpQuery::newDocumentHTML($html);
        $images = $document->find('img[data-src]');
        foreach ($images as $image)
        {
            $src = pq($image)->attr('data-src');
            pq($image)->attr('data-src', '/slides/' . $src);
        }
        return $document->find("div.reveal")->html();
    }

    public function exportSlideImagesFromDropBox()
    {
        $dropboxFolder = Yii::$app->params['dropboxSlidesPath'] . $this->alias;
        $contents = Yii::$app->dropbox->listContents($dropboxFolder);
        $localFolder = Yii::getAlias('@public') . '/slides/' . $this->alias . '/';
        if (!file_exists($localFolder))
        {
            mkdir($localFolder, 0777);
        }
        else
        {
            array_map('unlink', glob($localFolder . "*.jpg"));
        }
        foreach ($contents as $content)
        {
            $data = Yii::$app->dropbox->read($content["path"]);
            file_put_contents($localFolder . $content["basename"], $data);
            $data = null;
        }
    }

    public function exportSlideFromDropBox()
    {
        $this->body = $this->exportSlideBodyFromDropBox();
        $this->exportSlideImagesFromDropBox();
    }

    public function getImagesFolderPath($web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides/' . $this->alias;
    }

    public function getStoryImages()
    {
        $dir  = opendir($this->getImagesFolderPath());
        $images = [];
        while (false !== ($filename = readdir($dir)))
        {
            if (!in_array($filename, array('.', '..'))) 
            {
                $images[] = $this->getImagesFolderPath(true) . '/' . $filename;
            }
        }
        return $images;
    }

    public static function findStories()
    {
        return self::find()->published();
    }

    public function getCategoryLink()
    {
        $category = $this->getCategory()->one();
        return Html::a($category->name, ['story/category', 'category' => $category->alias]);
    }

}
