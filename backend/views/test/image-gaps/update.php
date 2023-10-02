<?php

declare(strict_types=1);

use backend\assets\SvgAsset;
use backend\assets\TestQuestionAsset;
use backend\Testing\Questions\ImageGaps\Update\UpdateImageGapsForm;
use backend\widgets\QuestionManageWidget;
use common\assets\panzoom\PanzoomAsset;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $quizModel
 * @var UpdateImageGapsForm $formModel
 * @var StoryTestQuestion $questionModel
 * @var array{
 *     url: string,
 *     width: int,
 *     height: int
 * } $imageParams
 */

$this->title = 'Изменить вопрос';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $quizModel->source]],
    ['label' => $quizModel->title, 'url' => ['test/update', 'id' => $quizModel->id]],
    $this->title,
];

SvgAsset::register($this);
TestQuestionAsset::register($this);
PanzoomAsset::register($this);

$url = $imageParams['url'];
$width = $imageParams['width'];
$height = $imageParams['height'];
$payload = empty($questionModel->regions) ? '{fragments: [], content: ""}' : $questionModel->regions;
$this->registerJs(<<<JS
window.imageParams = {
    url: '$url',
    width: $width,
    height: $height
}
window.imageGapsPayload = $payload
JS
, View::POS_HEAD);

$this->registerJs($this->renderFile('@backend/views/test/image-gaps/_image_gaps.js'));
?>
<?= QuestionManageWidget::widget([
    'quizModel' => $quizModel,
    'currentModelId' => $questionModel->id,
    'renderData' => $this->render('_update_question', [
        'formModel' => $formModel,
        'testingId' => $quizModel->id,
        'imageParams' => $imageParams
    ]),
]); ?>
