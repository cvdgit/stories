<?php

declare(strict_types=1);

namespace frontend\modules\repetition\controllers;

use frontend\modules\repetition\Finish\FinishAction;
use yii\web\Controller;

class TestingController extends Controller
{
    public function actions(): array
    {
        return [
            'finish' => FinishAction::class,
        ];
    }
}
