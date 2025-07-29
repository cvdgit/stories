<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\AudioFile;
use CURLFile;
use DomainException;
use Exception;
use frontend\Transcriptions\TranscriptionsForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class AudioController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionTranscriptions(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $model = new TranscriptionsForm();
        if ($model->load($request->post(), '')) {
            $model->audio = UploadedFile::getInstanceByName('audio');
            if (!$model->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            $folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
            try {
                $audioFolder = AudioFile::getAudioFilesPath($folder);
                FileHelper::createDirectory($audioFolder);

                $fileName = $model->audio->baseName . '.' . $model->audio->extension;
                if (!$model->audio->saveAs($audioFolder . '/' . $fileName)) {
                    throw new DomainException('Audio save error');
                }

                $apiResponse = $this->apiLocalRequest($audioFolder . '/' . $fileName);

                return ['success' => true, 'data' => Json::decode($apiResponse)];
            } catch (Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false];
    }

    private function apiLocalRequest(string $filePath): string
    {
        $file = new CURLFile($filePath);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['whisper.api.host']);
        curl_setopt($ch, CURLOPT_POST, 1);

        $data = [
            'file' => $file,
            // 'model' => 'whisper-1',
            // 'response_format' => 'text',
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new DomainException('Request Error:' . curl_error($ch));
        }

        curl_close($ch);

        if ($response !== false) {
            return $response;
        }
        return '';
    }
}
