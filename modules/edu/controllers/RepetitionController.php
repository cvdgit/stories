<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\models\StoryTest;
use modules\edu\models\MentalMap;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RepetitionController extends Controller
{
    public $layout = '@frontend/views/layouts/edu';

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuiz(int $id): string
    {
        $testing = StoryTest::findOne($id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $studentId = Yii::$app->studentContext->getId();
        if ($studentId === null) {
            $studentId = Yii::$app->user->identity->getStudentID();
        }

        return $this->render('quiz', [
            'testing' => $testing,
            'studentId' => $studentId,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionMentalMap(string $id): string
    {
        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            throw new NotFoundHttpException('Ментальная карта не найдена');
        }

        $storyId = null;
        $slideId = null;
        $row = (new Query())
            ->select(['story_id', 'slide_id'])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.mental_map_id' => $mentalMap->uuid,
            ])
            ->andWhere('h.story_id IS NOT NULL')
            ->one();
        if (!empty($row)) {
            $storyId = (int) $row['story_id'];
            $slideId = (int) $row['slide_id'];
        }

        return $this->render('mental_map', [
            'storyId' => $storyId,
            'slideId' => $slideId,
            'mentalMap' => $mentalMap,
        ]);
    }
}
