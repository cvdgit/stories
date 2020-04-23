<?php


namespace backend\models\editor;


class CropImageForm extends BaseForm
{

    public $croppedImage;
    public $croppedImageID;
    public $what;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['croppedImage', 'image', 'maxSize' => 50 * 1024 * 1024],
            [['croppedImageID', 'what'], 'string'],
        ]);
        return $rules;
    }

    public function upload(string $path)
    {
        $this->croppedImage->saveAs($path);
    }

}