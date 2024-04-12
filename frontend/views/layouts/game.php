<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $content
 */

$this->registerCss(<<<CSS
body { padding: 0; margin: 0 }
#unity-container { position: absolute }
#unity-container.unity-desktop { left: 50%; top: 50%; transform: translate(-50%, -50%) }
#unity-container.unity-mobile { position: fixed; width: 100%; height: 100% }
#unity-canvas { background: #231F20 }
.unity-mobile #unity-canvas { width: 100%; height: 100% }
#unity-loading-bar { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: none }
#unity-logo { width: 154px; height: 130px; background: url('/game/TemplateData/unity-logo-dark.png') no-repeat center }
#unity-progress-bar-empty { width: 141px; height: 18px; margin-top: 10px; margin-left: 6.5px; background: url('/game/TemplateData/progress-bar-empty-dark.png') no-repeat center }
#unity-progress-bar-full { width: 0%; height: 18px; margin-top: 10px; background: url('/game/TemplateData/progress-bar-full-dark.png') no-repeat center }
#unity-footer { position: relative }
.unity-mobile #unity-footer { display: none }
#unity-webgl-logo { float:left; width: 204px; height: 38px; background: url('/game/TemplateData/webgl-logo.png') no-repeat center }
#unity-build-title { float: right; margin-right: 10px; line-height: 38px; font-family: arial; font-size: 18px }
#unity-fullscreen-button { cursor:pointer; float: right; width: 38px; height: 38px; background: url('/game/TemplateData/fullscreen-button.png') no-repeat center }
#unity-warning { position: absolute; left: 50%; top: 5%; transform: translate(-50%); background: white; padding: 10px; display: none }
CSS
);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
