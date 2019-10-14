<?php


namespace common\models;


use Yii;
use yii\base\Model;

class BaseAudioTrackModel extends Model
{

    public $story_id;
    public $user_id;
    public $name;
    public $type;
    public $default;

    public function rules()
    {
        return [
            [['story_id', 'user_id', 'type', 'name'], 'required'],
            [['story_id', 'user_id', 'type', 'default'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'story_id' => 'История',
            'user_id' => 'Автор',
            'type' => 'Тип',
            'default' => 'По умолчанию',
            'name' => 'Заголовок',
        ];
    }

    public static function getTrackRelativePath(int $storyID, int $trackID)
    {
        return '/audio/' . $storyID . DIRECTORY_SEPARATOR . $trackID;
    }

    public static function getTrackPath(int $storyID, int $trackID)
    {
        return Yii::getAlias('@public') . self::getTrackRelativePath($storyID, $trackID);
    }

    public static function trackFileList(int $storyID, int $trackID): array
    {
        $files = [];
        $path = self::getTrackPath($storyID, $trackID);
        if (file_exists($path)) {
            $dir = opendir($path);
            while (false !== ($filename = readdir($dir))) {
                if (!in_array($filename, array('.', '..'))) {
                    $files[] = $filename;
                }
            }
        }
        return $files;
    }

}