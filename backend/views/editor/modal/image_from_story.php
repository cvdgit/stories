<?php
use dosamigos\selectize\SelectizeTextInput;
?>
<div class="modal fade" id="image-from-story-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Добавить изображение на слайд из истории</h4>
            </div>
            <div class="modal-body">
                <div style="padding: 20px 0">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <?= SelectizeTextInput::widget([
                                'name' => 'imageStory',
                                'loadUrl' => ['story/autocomplite'],
                                'clientOptions' => [
                                    'valueField' => 'id',
                                    'labelField' => 'title',
                                    'searchField' => ['title'],
                                    'maxItems' => 1,
                                    'render' => [
                                        'option' => new \yii\web\JsExpression('function(item, escape) {
                                                return "<div class=\"media\" style=\"padding:10px\">" +
                                                            "<div class=\"media-left\">" +
                                                                "<img alt=\"cover\" height=\"64\" class=\"media-object\" src=\"" + item.cover + "\" />" +
                                                            "</div>" +
                                                            "<div class=\"media-body\">" +
                                                                "<p class=\"media-heading\">" + item.title + "</p>" +
                                                            "</div>" +
                                                       "</div>";
                                            }')
                                    ],
                                    'onChange' => new \yii\web\JsExpression('ImageFromStory.changeImageStory'),
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div id="story-images-list" class="row" style="margin-top: 20px"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
