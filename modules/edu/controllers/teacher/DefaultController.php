<?php

declare(strict_types=1);


namespace modules\edu\controllers\teacher;

use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduProgram;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{

    public function actionIndex()
    {

        $classBooks = EduClassBook::findTeacherClassBooks(Yii::$app->user->getId())
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'classBooks' => $classBooks,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionClassProgramStats(int $class_book_id, int $class_program_id)
    {

        if (($classBook = EduClassBook::findOne($class_book_id)) === null) {
            throw new NotFoundHttpException('Класс не найден');
        }

        if (($classProgram = EduClassProgram::findOne($class_program_id)) === null) {
            throw new NotFoundHttpException('Программа не найдена');
        }

        return $this->render('stats', [
            'classBook' => $classBook,
            'classProgram' => $classProgram,
        ]);
    }
}
