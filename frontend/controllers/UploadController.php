<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use frontend\models\UploadForm;
use yii\web\UploadedFile;

class UploadController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
 
    public function beforeAction($action)
    {
        if ($action->id === 'file-avatar') {    
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    } 
     
    public function actionFileAvatar()
    {         
        $model = new UploadForm;
        $model->imageFile = UploadedFile::getInstanceByName('image-upload');   
        $model->myUpload();                    
    }

}