<?php

namespace common\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "news".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $text
 * @property int $status
 * @property int $user_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class News extends ActiveRecord
{

    const STATUS_PROPOSED = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_REJECTED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'news';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'text', 'status'], 'required'],
            [['text'], 'string'],
            [['status', 'user_id'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Заголовок',
            'slug' => 'Alias',
            'text' => 'Текст',
            'status' => 'Статус',
            'user_id' => 'Автор',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string status as string
     */
    public function getStatusLabel()
    {
        return static::statusLabel($this->status);
    }

    /**
     * Returns a string representation of status
     *
     * @param int $status
     * @return string
     */
    public static function statusLabel($status)
    {
        $statuses = static::getStatuses();
        return ArrayHelper::getValue($statuses, $status);
    }

    /**
     * @return array statuses available
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PROPOSED => 'Черновик',
            self::STATUS_PUBLISHED => 'Опубликовано',
            self::STATUS_REJECTED => 'Удалено',
        ];
    }

}
