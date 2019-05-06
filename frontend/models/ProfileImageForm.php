<?php


namespace frontend\models;


use yii\base\Model;
use yii\web\UploadedFile;

class ProfileImageForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    public function rules(): array
    {
        return [
            ['file', 'image'],
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->file = UploadedFile::getInstance($this, 'file');
            return true;
        }
        return false;
    }

}