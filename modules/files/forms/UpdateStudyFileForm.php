<?php

namespace modules\files\forms;

use modules\files\models\StudyFile;
use modules\files\models\StudyFileStatus;
use modules\files\models\StudyFolder;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UpdateStudyFileForm extends Model
{

    public $file;
    public $name;
    public $alias;
    public $folder_id;
    public $status;

    private $model;

    public function __construct(StudyFile $model, $config = [])
    {
        $this->model = $model;
        $this->loadAttributes();
        parent::__construct($config);
    }

    private function loadAttributes(): void
    {
        $this->name = $this->model->name;
        $this->alias = $this->model->alias;
        $this->folder_id = $this->model->folder_id;
        $this->status = $this->model->status;
    }

    public function rules(): array
    {
        return [
            [['name', 'folder_id', 'status'], 'required'],
            ['file', 'file'],
            [['folder_id', 'status'], 'integer'],
            [['name', 'alias'], 'string', 'max' => 255],
            [['folder_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyFolder::class, 'targetAttribute' => ['folder_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'file' => 'Файл',
            'name' => 'Название',
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
