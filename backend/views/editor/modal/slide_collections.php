<?php
use yii\helpers\Html;
?>
<div class="modal fade" id="slide-collections-modal" style="z-index: 1051">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Коллекции</h4>
            </div>
            <div class="modal-body">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#story-collection" aria-controls="story-collection" role="tab" data-toggle="tab">Коллекции истории</a></li>
                        <li role="presentation"><a href="#yandex-collection" aria-controls="yandex-collection" role="tab" data-toggle="tab">Добавить из яндекс коллекции</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="story-collection">
                            <div class="collection_list" style="margin: 20px 0"></div>
                            <div class="row collection_card_list" style="margin-top: 20px"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="yandex-collection">
                            <div class="clearfix" style="padding-top: 20px">
                                <div class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Аккаунт
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                            <?php foreach (array_keys(Yii::$app->params['yandex.accounts']) as $account): ?>
                                                <li><?= Html::a($account, '#', ['data-account' => $account]) ?></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <ul class="pagination pagination-lg" id="collection-page-list"></ul>
                            <div class="collection_list"></div>
                            <div class="row collection_card_list" style="margin-top: 20px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
