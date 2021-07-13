<?php
use common\models\StoryTest;
use yii\helpers\Html;
/** @var $testModel StoryTest */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Тесты</h4>
</div>
<div class="modal-body">
    <div class="tests-manage">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input id="tests-filter" type="text" class="form-control" placeholder="Фильтр тестов" autocomplete="off">
                </div>
                <ul class="list-group tests-manage-test-list" id="all-tests-list">
                    <?php foreach (StoryTest::getLocalTestOnlyArray($testModel->relatedTests) as $testID => $testTitle): ?>
                    <li class="list-group-item">
                        <span class="text-wrapper" title="<?= Html::encode($testTitle) ?>"><?= Html::encode($testTitle) ?></span> <span data-test-id="<?= $testID ?>" class="badge"><i class="glyphicon glyphicon-plus"></i></span>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group tests-manage-test-list" style="margin-top: 49px" id="selected-tests-list">
                    <?php foreach ($testModel->relatedTests as $test): ?>
                        <li class="list-group-item">
                            <span class="text-wrapper" title="<?= Html::encode($test->title) ?>"><?= Html::encode($test->title) ?></span> <span data-test-id="<?= $test->id ?>" class="badge"><i class="glyphicon glyphicon-minus"></i></span>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" id="save-selected-tests">Сохранить</button>
    <button class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
