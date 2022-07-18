<?php

namespace modules\files\forms;

use modules\files\models\StudyFileStatus;
use modules\files\models\StudyFolder;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CreateStudyFileForm extends Model
{

    public $file;
    public $alias;
    public $folder_id;
    public $status;

    public function rules(): array
    {
        return [
            [['file', 'folder_id', 'status'], 'required'],
            ['file', 'file'],
            [['folder_id', 'status'], 'integer'],
            [['alias'], 'string', 'max' => 255],
            [['folder_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyFolder::class, 'targetAttribute' => ['folder_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'file' => 'Файл',
            'alias' => 'Alias',
            'folder_id' => 'Папка',
            'status' => 'Статус',
        ];
    }

    public function getFolderItems(): array
    {
        return ArrayHelper::map(StudyFolder::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public function getStatusItems(): array
    {
        return StudyFileStatus::asArray();
    }
}
