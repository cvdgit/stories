<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use yii\helpers\Json;
use yii\web\Request;

class MentalMapController extends BaseStreamController
{
    public function actionTextFragments(Request $request): void
    {
        $payload = Json::decode($request->rawBody);
        $text = $payload['text'];

        $content = <<<TEXT
<text>
$text
</text>

Разбей весь текст на фрагменты по смыслу не больше 200 символов на фрагмент.
Ничего не убирай.
Ответь в формате json.
Пример: ["текст фрагмента 1", "текст фрагмента 2"]
TEXT;

        $fields = $this->createFieldsPayload($content);
        $this->sendStream('text-create-fragments', Json::encode($fields));
    }
}
