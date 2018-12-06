<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class UploadForm extends Model
{
    
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function myUpload()
    {       
        if ($this->validate()) { 
            
            $names='';        
            $filename = Yii::$app->getSecurity()->generateRandomString(15);                   
            $this->imageFile->saveAs('uploads/' . $filename . '.' . $this->imageFile->extension);
            $names.= $filename . '.' . $this->imageFile->extension;
            
            $id = Yii::$app->user->id;
            $user = User::findOne($id);
            if (!$user) {
                throw new \DomainException('User is not found.');
            }
            $user->image = $names;
            $user->update();
            if (!$user->save()) {
                throw new \RuntimeException('Update error.');
            }
            
            return true;
        } else {
            return false;
        }
    } 

}