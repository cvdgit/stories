<?php

namespace backend\widgets;

use common\components\StoryCover;
use common\models\Story;
use common\models\UserRecentStory;
use dosamigos\selectize\SelectizeDropDownList;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JsExpression;

class SelectStoryWidget extends Widget
{

    /** @var Story */
    public $storyModel;

    public $showRecentStories = false;

    public $model;
    public $attribute;

    public $linkedSlidesId;
    public $selectedSlideId;

    public $onChange = '{}';

    public $loadUrl = ['/story/autocomplete/select'];

    private $widgetOptions;
    private $clientOptions = [
        'valueField' => 'id',
        'labelField' => 'title',
        'searchField' => ['title'],
        'maxItems' => 1,
        'maxOptions' => 30,
        'persist' => false,
        'create' => false,
        'openOnFocus' => true,
        'highlight' => true,
        'scrollDuration' => 60,
        'render' => [],
    ];

    public function init()
    {
        $this->widgetOptions = [
            'name' => 'selectStory',
            'id' => $this->id,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'loadUrl' => $this->loadUrl,
        ];

        if ($this->storyModel !== null) {
            $this->widgetOptions['items'] = [$this->storyModel->id => $this->storyModel->title];
            $this->widgetOptions['options'] = [
                'options' => [
                    $this->storyModel->id => [
                        'data-data' => $this->getOptionData($this->storyModel->id, $this->storyModel->title, StoryCover::getListThumbPath($this->storyModel->cover)),
                    ],
                ],
            ];
        }
        else {
            if ($this->showRecentStories) {
                $this->widgetOptions['options']['prompt'] = '';
                $recentStories = UserRecentStory::getUserRecentStories(Yii::$app->user->id);
                $options = [];
                foreach ($recentStories as $storyModel) {
                    $this->widgetOptions['items'][$storyModel->id] = $storyModel->title;
                    $options[$storyModel->id] = [
                        'data-data' => $this->getOptionData($storyModel->id, $storyModel->title, StoryCover::getListThumbPath($storyModel->cover)),
                    ];
                }
                $this->widgetOptions['options']['options'] = $options;
            }
        }

        $this->clientOptions['onChange'] = new JsExpression($this->onChange);
        if ($this->linkedSlidesId !== null) {
            $this->clientOptions['onChange'] = $this->onChangeExpression($this->linkedSlidesId, $this->selectedSlideId);
        }

        $this->clientOptions['render']['option'] = $this->renderOptionExpression();
        $this->widgetOptions['clientOptions'] = $this->clientOptions;
    }

    public function run()
    {
        return SelectizeDropDownList::widget($this->widgetOptions);
    }

    private function renderOptionExpression(): JsExpression
    {
        /** @noinspection SyntaxError */
        return new JsExpression(<<<JS
            function(item, escape) {
                return "<div class=\"media\" style=\"padding:10px\">" +
                         "<div class=\"media-left\">" +
                           "<img alt=\"cover\" height=\"64\" class=\"media-object\" src=\"" + item.cover + "\" />" +
                         "</div>" +
                         "<div class=\"media-body\">" +
                           "<p class=\"media-heading\">" + item.title + "</p>" +
                         "</div>" +
                       "</div>";
            }
JS
        );
    }

    private function getOptionData(int $id, string $title, $cover = ''): string
    {
        return Json::encode([
            'id' => $id,
            'title' => $title,
            'cover' => $cover,
        ]);
    }

    private function onChangeExpression(string $elementID, $selectedSlideId = null): JsExpression
    {
        $selectedSlideId = mb_strtolower(var_export($selectedSlideId, true));
        return new JsExpression(<<<JS
            function(storyID) {
                var slidesDropDown = $('#$elementID');
                var selectedSlideID = $selectedSlideId;
                if (selectedSlideID) {
                    selectedSlideID = parseInt(selectedSlideID);
                }
                slidesDropDown.empty();
                if (!storyID.length) {
                    return;
                }
                $.getJSON('/admin/index.php', {
                    'r': 'editor/slides',
                    'story_id': storyID
                })
                    .done(function(data) {
                        $('<option/>')
                            .text('Выберите слайд')
                            .val('')
                            .appendTo(slidesDropDown);
                        data.forEach(function(slide) {
                            $('<option/>')
                                .val(slide.id)
                                .text('Слайд ' + slide.slideNumber + (slide.isHidden ? ' (скрытый)' : ''))
                                .prop('selected', selectedSlideID === slide.id)
                                .appendTo(slidesDropDown);
                        });
                    });
            }
JS
        );
    }
}
