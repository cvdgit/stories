<?php

namespace common\models;

use backend\models\links\BlockType;
use common\components\StoryCover;
use common\helpers\Url;
use common\models\story\StoryStatus;
use DomainException;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use dosamigos\taggable\Taggable;
use common\helpers\Translit;
use yii\db\Expression;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\db\ActiveQuery;

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
 * @property int $audio
 * @property int $user_audio
 * @property int $episode
 * @property int $video
 * @property int $published_at
 * @property int $have_neo_relation
 * @property int $access_by_link;
 *
 * @property User $author
 * @property Tag[] $tags
 * @property Category[] $categories
 * @property Comment[] $comments
 * @property StoryAudioTrack[] $storyAudioTracks
 * @property Playlist[] $playlists
 * @property StoryStoryTest[] $storyStoryTests
 * @property StoryTest[] $tests
 * @property StorySlide[] $storySlides
 * @property StorySlideImage[] $storyImages
 */

class Story extends ActiveRecord
{

    const SOURCE_SLIDESCOM = 1;
    const SOURCE_POWERPOINT = 2;

    public $source_dropbox = '';
    public $source_powerpoint = '';

    public $story_categories;
    public $story_playlists;

    public $playlist_order;

    public static function tableName()
    {
        return '{{%story}}';
    }

    public static function find()
    {
        return new StoryQuery(static::class);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => Taggable::class,
            ],
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'categories',
                    'playlists',
                ],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['title', 'alias', 'user_id', 'source_id', 'story_categories'], 'required'],
            [['title'], 'trim'],
            [['body', 'cover', 'story_file', 'source_dropbox', 'source_powerpoint'], 'string'],
            [['user_id', 'sub_access', 'source_id', 'views_number', 'slides_number', 'audio', 'published_at'], 'integer'],
            [['video', 'user_audio', 'episode', 'access_by_link'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['status', 'in', 'range' => [StoryStatus::DRAFT, StoryStatus::PUBLISHED, StoryStatus::FOR_PUBLICATION]],
            ['status', 'default', 'value' => StoryStatus::DRAFT],
            [['tagNames', 'story_playlists', 'story_categories'], 'safe'],
            [['description'], 'string', 'max' => 1024],
            ['source_id', 'default', 'value' => self::SOURCE_POWERPOINT],
        ];
    }

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
            'story_categories' => 'Категории',
            'sub_access' => 'По подписке',
            'cover' => 'Обложка',
            'story_file' => 'Файл PowerPoint',
            'description' => 'Краткое описание',
            'source_id' => 'Источник',
            'source_dropbox' => 'Имя истории в Slides.com',
            'source_powerpoint' => 'Файл PowerPoint (pptx)',
            'views_number' => 'Просмотров',
            'audio' => 'История с озвучкой',
            'episode' => 'Эпизод',
            'story_playlists' => 'Плейлисты',
            'published_at' => 'Дата публикации истории',
            'access_by_link' => 'Доступ по ссылке',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('story_category', ['story_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPlaylists()
    {
        return $this->hasMany(Playlist::class, ['id' => 'playlist_id'])->viaTable('story_playlist', ['story_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('{{%story_tag}}', ['story_id' => 'id']);
    }

    public function getComments()
    {
        return $this->hasMany(Comment::class, ['story_id' => 'id']);
    }

    /*public static function findStories()
    {
        return self::find();
    }*/

    /**
     * @return StoryQuery
     */
    public static function findPublishedStories()
    {
        return self::find()->published()->with('categories')->with('userStoryHistories');
    }

    public static function findPublishedStoriesModerator()
    {
        return self::find()->with('categories')->with('userStoryHistories');
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
        return self::find()->published()->lastStories()->with(['categories', 'userStoryHistories'])->all();
    }

    public static function followingStories(array $categoryIDs)
    {
        return self::find()->published()->byCategories($categoryIDs)->byRand()->all();
    }

    public static function oneRandomStory()
    {
        return self::find()->published()->byRand()->limit(1)->one();
    }

    /*public static function getSourceList()
    {
        return [
            self::SOURCE_SLIDESCOM => 'Slides.com (Dropbox)',
            self::SOURCE_POWERPOINT => 'Файл PowerPoint (PPTX)',
        ];
    }*/

    public function saveBody($body)
    {
        $this->body = $body;
        return $this->save(false);
    }

    public function bySubscription()
    {
        return !empty($this->sub_access);
    }

    /*public static function findStory($condition)
    {
        return static::findByCondition($condition)->published()->one();
    }*/

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

    /**
     * @param $alias
     * @return Story
     * @throws NotFoundHttpException
     */
    public static function findModelByAlias($alias): self
    {
        if (($model = self::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('История не найдена');
    }

    public function fillStoryCategories(): void
    {
        $categories = [];
        foreach ($this->categories as $category) {
            $categories[] = $category->id;
        }
        $this->story_categories = implode(',', $categories);
    }

    public function fillStoryPlaylists(): void
    {
        $playlists = [];
        foreach ($this->playlists as $playlist) {
            $playlists[] = $playlist->id;
        }
        $this->story_playlists = implode(',', $playlists);
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

    public function isPublished(): bool
    {
        return ((int)$this->status === StoryStatus::PUBLISHED);
    }

    public function isOriginalAudioTrack(): bool
    {
        return $this->getOriginalTrack() !== false;
    }

    public function audioTrackPublished()
    {
        if (!($track = $this->getOriginalTrack())) {
            return false;
        }
        return $track->isPublished();
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getHistoryUser()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_story_history', ['story_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStorySlides()
    {
        return $this->hasMany(StorySlide::class, ['story_id' => 'id'])->orderBy(['number' => SORT_ASC]);
    }

    private static function modifySlideData(int $id, string $data): string
    {
        $search = [
            'data-id=""',
            'data-background-color="#000000"',
        ];
        $replace = [
            'data-id="' . $id . '"',
            'data-background-color="#fff"',
        ];
        return str_replace($search, $replace, $data);
    }

    public function getSlidesForQuestion(): array
    {
        $models = $this->getStorySlides()
            ->where('kind = :kind', [':kind' => StorySlide::KIND_SLIDE])
            ->orderBy(['number' => SORT_ASC])
            ->all();
        return self::modifySlides($models);
    }

    public static function modifySlides(array $models): array
    {
        return array_map(static function(StorySlide $slide) {
            return [
                'id' => $slide->id,
                'slideNumber' => $slide->number,
                'data' => self::modifySlideData($slide->id, $slide->data),
            ];
        }, $models);
    }

    public function slidesData(bool $withoutQuestions = false): string
    {
        $slides = (new Query())->from('{{%story_slide}} AS t1')
            ->select(['t1.data', 't2.data AS link_data', 't1.id'])
            ->leftJoin('{{%story_slide}} t2', 't2.id = t1.link_slide_id')
            ->where('t1.`story_id` = :story', [':story' => $this->id])
            ->andWhere('t1.`status` = :status', [':status' => 1])
            ->orderBy(['t1.number' => SORT_ASC]);
        if ($withoutQuestions) {
            $slides->andWhere('t1.`kind` <> :kind', [':kind' => StorySlide::KIND_QUESTION]);
        }
        $slides = $slides->all();
        $data = '';
        foreach ($slides as $slide) {
            $data .= $slide['link_data'] ?? $slide['data'];
            $search = [
                'data-id=""',
                'data-background-color="#000000"',
            ];
            $replace = [
                'data-id="' . $slide['id'] . '"',
                'data-background-color="#fff"',
            ];
            $data = str_replace($search, $replace, $data);
        }
        return '<div class="slides">' . $data . '</div>';
    }

    public function isAudioStory(): bool
    {
        return (int)$this->audio === 1;
    }

    public function haveVideo(): bool
    {
        return (int)$this->video === 1;
    }

    public function isUserAudioStory($userID): bool
    {
        if ($userID === null) {
            return false;
        }
        $trackArray = array_filter($this->storyAudioTracks, function(StoryAudioTrack $model) use ($userID) {
            return $model->isUserTrack($userID);
        });
        return count($trackArray) > 0;
    }

    public function slideBlocksData()
    {
        return (new Query())
            ->select('t1.id AS slideID, t2.title, t2.href')
            ->from('{{%story_slide}} AS t1')
            ->innerJoin('{{%story_slide_block}} t2', 't1.id = t2.slide_id')
            ->where('t1.story_id = :story', [':story' => $this->id])
            ->andWhere('t1.status = :status', [':status' => 1])
            ->andWhere(['in', 't2.type', [BlockType::BUTTON, BlockType::YOUTUBE]])
            //->indexBy('slideID')
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryAudioTracks()
    {
        return $this->hasMany(StoryAudioTrack::class, ['story_id' => 'id']);
    }

    public function checkAudioTracks()
    {
        $audio = $this->storyAudioTracks ? 1 : 0;
        $this->audio = $audio;
        $this->save(false);
    }

    public function getOriginalTrack()
    {
        $trackArray = array_filter($this->storyAudioTracks, function(StoryAudioTrack $model) {
            return ($model->isOriginal() && $model->isDefault());
        });
        return current($trackArray);
    }

    public function getUserTrack($userID)
    {
        $trackArray = array_filter($this->storyAudioTracks, function(StoryAudioTrack $model) use ($userID) {
            return $model->isUserTrack($userID);
        });
        return current($trackArray);
    }

    public function getStoryTrack($userID)
    {
        $track = $this->getOriginalTrack();
        if (!$track) {
            $track = $this->getUserTrack($userID);
        }
        return $track;
    }

    public function getUserAudioTracks($userID)
    {
        return array_filter($this->storyAudioTracks, function(StoryAudioTrack $track) use ($userID) {
            return ($track->isOriginal() && $track->isPublished()) || $track->isUserTrack($userID);
        });
    }

    public static function updateSlideNumber(int $storyID)
    {
        $model = self::findModel($storyID);
        $slideNumber = (new Query())
            ->from('{{%story_slide}}')
            ->where('story_id = :story', [':story' => $model->id])
            ->count();
        $model->slides_number = $slideNumber;
        $model->save(false);
    }

    public function storyFacts()
    {
        $storySlidesQuery = (new Query())
            ->from('{{%story_slide}}')
            ->where('story_id = :story', [':story' => $this->id])
            ->select(['id']);
        return (new Query())
            ->from('{{%story_slide_block}}')
            ->where(['in', 'slide_id', $storySlidesQuery])
            ->select(['title'])
            ->all();
    }

    public function getBaseModel()
    {
        return new StoryModel($this);
    }

    public static function updateVideo(int $storyID, int $video)
    {
        $model = self::findModel($storyID);
        $model->video = $video;
        $model->save(false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserStoryHistories()
    {
        return $this->hasOne(UserStoryHistory::class, ['story_id' => 'id'])
            ->andWhere('user_id = :user', [':user' => Yii::$app->user->id]);
    }

    public function publishStory(): void
    {
        $this->status = StoryStatus::PUBLISHED;
        if ($this->published_at === null) {
            $this->published_at = time();
        }
        $this->save(false);
    }

    public function storyToPublish(): void
    {
        $this->status = StoryStatus::FOR_PUBLICATION;
        $this->published_at = time();
        $this->save(false);
    }

    public function isForPublication(): bool
    {
        return $this->status === StoryStatus::FOR_PUBLICATION;
    }

    public function submitPublicationTask(): bool
    {
        return ($this->published_at === null);
    }

    public function hasNeoRelation(): bool
    {
        return (int)$this->have_neo_relation === 1;
    }

    public static function updateNeoRelationValue(int $storyID, int $value)
    {
        $model = self::findModel($storyID);
        $model->have_neo_relation = $value;
        $model->save(false);
    }

    public function updateAudioFlag(int $flag)
    {
        $this->audio = $flag;
        return $this->save(false);
    }

    public static function create(string $title, int $userID, array $categories)
    {
        $model = new self();
        $model->loadDefaultValues();
        $model->category_id = 1;
        $model->title = $title;
        $model->user_id = $userID;
        $model->source_id = self::SOURCE_POWERPOINT;
        $model->story_categories = implode(',', $categories);
        $model->categories = $categories;
        return $model;
    }

    public function getStoryUrl()
    {
        return Url::toRoute(['/story/view', 'alias' => $this->alias]);
    }

    public function getListThumbPath()
    {
        return empty($model->cover) ? '/img/story-1.jpg' : StoryCover::getListThumbPath($this->cover);
    }

    public static function findWithQuestionSlide(array $ids)
    {
        $existsQuery = (new Query())
            ->select([new Expression('1')])
            ->from(['t2' => StorySlide::tableName()])
            ->where('t2.story_id = t.id');
        return self::find()
            ->from(['t' => self::tableName()])
            ->where(['in', 'id', $ids])
            ->andWhere(['exists', $existsQuery])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoryStoryTests()
    {
        return $this->hasMany(StoryStoryTest::class, ['story_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTests()
    {
        return $this->hasMany(StoryTest::class, ['id' => 'test_id'])->viaTable('story_story_test', ['story_id' => 'id']);
    }

    public static function getToPublishStories()
    {
        return self::find()
            ->where('status = :status', [':status' => StoryStatus::FOR_PUBLICATION])
            ->orderBy(['published_at' => SORT_ASC])
            ->all();
    }

    public function cancelPublication(): void
    {
        if ($this->isPublished()) {
            throw new DomainException('Невозможно отменить т.к. история уже опубликована');
        }
        $this->status = StoryStatus::DRAFT;
        $this->published_at = null;
        $this->save(false, ['status', 'published_at']);
    }

    public function linkAccessAllowed(): bool
    {
        return $this->access_by_link === 1;
    }

    public function grantLinkAccess(): string
    {
        $this->access_by_link = 1;
        return $this->save(false);
    }

    public function revokeLinkAccess(): string
    {
        $this->access_by_link = 0;
        return $this->save(false);
    }

    public function getPreviewUrl()
    {
        return Yii::$app->urlManagerFrontend->createUrl(['preview/view', 'alias' => $this->alias]);
    }

    public function getStoryFilePath(bool $abs = true): string
    {
        if (empty($this->story_file)) {
            throw new DomainException('Story file is empty');
        }
        return $this->getStoryFilesFolder($abs) . '/' . $this->story_file;
    }

    public function getSlideImagesFolder(): string
    {
        $folder = $this->id;
        if (!empty($this->story_file)) {
            $folder = $this->story_file;
        }
        return $folder;
    }

    public function getSlideImagesPath($abs = true): string
    {
        $folder = $this->getSlideImagesFolder();
        return ($abs ? Yii::getAlias('@public') : '') . '/slides/' . $folder;
    }

    public function getStoryFilesFolder(bool $abs = true): string
    {
        return ($abs ? Yii::getAlias('@public') : '') . '/slides_file';
    }

    public function getSlideIDs(): array
    {
        return array_map(static function(StorySlide $slide) {
            return $slide->id;
        }, $this->storySlides);
    }

    public function getStoryImages(): ActiveQuery
    {
        return $this->hasMany(StorySlideImage::class, ['id' => 'story_slide_image_id'])
            ->viaTable('story_story_slide_image', ['story_id' => 'id'])
            ->orderBy(['story_slide_image.created_at' => SORT_DESC]);
    }

    public function isStoryImage(StorySlideImage $image): bool
    {
        $storySlideIDs = $this->getSlideIDs();
        foreach ($image->slides as $slide) {
            if (in_array($slide->id, $storySlideIDs, true)) {
                return true;
            }
        }
        return false;
    }

    private const ACTION_SLIDE_INSERT = 1;
    private const ACTION_SLIDE_DELETE = 2;

    private static function updateSlidesOrder(int $action, int $storyID, int $currentNumber): void
    {
        $slides = (new Query())
            ->from('{{%story_slide}}')
            ->select(['id', 'number'])
            ->where('story_id = :story', [':story' => $storyID])
            ->orderBy(['number' => SORT_ASC])
            ->indexBy('id')
            ->all();
        $command = Yii::$app->db->createCommand();
        $next = $currentNumber + ($action === self::ACTION_SLIDE_INSERT ? 2 : 0);
        foreach ($slides as $slideID => $slide) {
            if ($slide['number'] > $currentNumber) {
                $command->update('{{%story_slide}}', ['number' => $next], 'id = :id', [':id' => $slideID]);
                $command->execute();
                $next++;
            }
        }
    }

    public static function insertSlideNumber(int $storyID, int $currentNumber): void
    {
        self::updateSlidesOrder(self::ACTION_SLIDE_INSERT, $storyID, $currentNumber);
    }

    public static function deleteSlideNumber(int $storyID, int $currentNumber): void
    {
        self::updateSlidesOrder(self::ACTION_SLIDE_DELETE, $storyID, $currentNumber);
    }
}
