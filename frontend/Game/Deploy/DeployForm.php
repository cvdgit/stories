<?php

declare(strict_types=1);

namespace frontend\Game\Deploy;

use yii\base\Model;

class DeployForm extends Model
{
    public $buildName;
    public $zipFile;

    public function rules(): array
    {
        return [
            [['buildName', 'zipFile'], 'required'],
            ['buildName', 'string', 'max' => 50],
            ['zipFile', 'file', 'extensions' => 'zip', 'maxSize' => 1024 * 1024 * 500],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'buildName' => 'Имя сборки',
            'zipFile' => 'Архив со сборкой (zip)',
        ];
    }
}
