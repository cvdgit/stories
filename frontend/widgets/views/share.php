<?php

/** @var $this yii\web\View */
/** @var $story common\models\Story */

use common\components\StoryCover; ?>
<div class="modal fade site-dialog" id="wikids-share-modal" tabindex="-1" role="dialog" style="top: 30%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin-bottom: 20px">Поделиться <span>историей</span></h4>
            </div>
            <div class="modal-body">
                <p>Отправить ссылку</p>
                <div
                    id="share" style="margin: 20px 0"
                    data-title="<?= $story->title ?>"
                    data-description="<?= $story->description ?>"
                    data-image="<?= 'https://wikids.ru' . StoryCover::getListThumbPath($story->cover) ?>"></div>
                <div>
                    <input id="share-link" type="text" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <div style="margin: 30px 70px; text-align: left">
                    <label for="share-slide-checkbox"><input id="share-slide-checkbox" type="checkbox"> Текущий слайд</label>
                </div>
            </div>
        </div>
    </div>
</div>
