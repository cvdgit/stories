<?php

namespace common\models;

use backend\components\FileBehavior;
use backend\components\queue\ChangeVideoJob;
use backend\models\video\VideoFolder;
use backend\models\video\VideoSource;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "slide_video".
 *
 * @property int $id
 * @property string $video_id
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property int $source;
 */
class SlideVideo extends ActiveRecord
{

    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'slide_video';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => '\yiidreamteam\upload\FileUploadBehavior',
                'attribute' => 'video_id',
                'filePath' => '@public'.Yii::$app->params['slides.videos'].'/[[pk]].[[extension]]',
                'fileUrl' => Yii::$app->params['slides.videos'].'/[[pk]].[[extension]]',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            //['video_id', 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'Видео',
            'title' => 'Название',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Updated At',
            'status' => 'Статус',
            'source' => 'Источник',
        ];
    }

    public static function videoArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['updated_at' => SORT_DESC])->where('source = :source', [':source' => VideoSource::YOUTUBE])->all(), 'video_id', 'title');
    }

    public static function videoFileArray(): array
    {
        $models = self::find()->orderBy(['updated_at' => SORT_DESC])->where('source = :source', [':source' => VideoSource::FILE])->all();
        $array = [];
        foreach ($models as $model) {
            $array[$model->getUploadedFileUrl('video_id')] = $model->title;
        }
        return $array;
    }

    public static function create(string $title, string $video_id, int $source): SlideVideo
    {
        $model = new self();
        $model->title = $title;
        $model->video_id = $video_id;
        $model->source = $source;
        return $model;
    }

    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Видео не найдено.');
    }

    public static function findModelByVideoID($videoID)
    {
        return self::findOne(['video_id' => $videoID]);
    }

    public function isSuccess()
    {
        return (int)$this->status === self::STATUS_SUCCESS;
    }

    protected function addJob(string $oldVideoID, string $newVideoID) {
        Yii::$app->queue->push(new ChangeVideoJob([
            'oldVideoID' => $oldVideoID,
            'newVideoID' => $newVideoID,
        ]));
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert && isset($changedAttributes['video_id']) && $changedAttributes['video_id'] !== $this->video_id) {
            $this->addJob($changedAttributes['video_id'], $this->video_id);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        if (VideoSource::isFile($this)) {
            $filePath = $this->getUploadedFileUrl('video_id');
            if (file_exists($filePath)) {
                FileHelper::unlink($filePath);
            }
        }
        parent::afterDelete();
    }
}
