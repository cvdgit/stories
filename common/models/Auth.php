<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth".
 *
 * @property int $id
 * @property int $user_id
 * @property string $source
 * @property string $source_id
 *
 * @property User $user
 */
class Auth extends ActiveRecord
{

    const AUTH_SESSION_KEY = 'authHandler';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'source' => 'Source',
            'source_id' => 'Source ID',
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $userID, string $source, string $sourceID): Auth
    {
        $model = new self();
        $model->user_id = $userID;
        $model->source = $source;
        $model->source_id = $sourceID;
        return $model;
    }

    public static function findAuth(string $source, string $sourceId): ?self
    {
        return self::find()->where([
            'source' => $source,
            'source_id' => $sourceId,
        ])->one();
    }
}
