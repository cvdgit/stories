<?php


namespace frontend\widgets;


use common\helpers\Url;
use common\services\StoryFavoritesService;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\JsExpression;

class StoryFavorites extends Widget
{

    public $storyId;

    protected $favoritesService;

    public function __construct(StoryFavoritesService $favoritesService, $config = [])
    {
        $this->favoritesService = $favoritesService;
        parent::__construct($config);
    }

    public function run()
    {
        $this->registerClientScript();

        $inFavorites = false;
        if (!Yii::$app->user->isGuest) {
            $inFavorites = $this->favoritesService->storyInFavorites(Yii::$app->user->id, $this->storyId);
        }

        $title = 'Добавить в избранное';
        $iconName = 'glyphicon-star-empty';
        $state = '';
        if ($inFavorites) {
            $iconName = 'glyphicon-star';
            $title = 'Убрать из избранного';
            $state = 'fav';
        }

        $icon = Html::tag('i', '', ['class' => 'glyphicon ' . $iconName]);
        $options = [
            'id' => 'wikids-favorites',
            'class' => 'btn-favorites',
            'title' => $title,
            'onclick' => new JsExpression('WikidsFavorites.favorites(this)'),
            'data-state' => $state,
            'data-loading-text' => '...',
        ];
        return Html::button($icon, $options);
    }

    protected function registerClientScript()
    {
        $action = Url::to(['story/add-favorites', 'story_id' => $this->storyId]);
        $needLogin = var_export(Yii::$app->user->isGuest, true);
        $js = <<< JS
WikidsFavorites = (function() {

    function send() {
        return $.ajax({
            "url": "$action",
            "type": "GET",
            "dataType": "json"
        });
    }
    
    function add(control) {
        send()
            .done(function(data) {
                if (data && data.success) {
                    control
                        .attr("data-state", "fav")
                        .attr("title", "Убрать из избранного")
                        .find("i")
                        .removeClass("glyphicon-star-empty glyphicon-star")
                        .addClass("glyphicon-star");
                    toastr.success("История добавлена в избранное", "Информация");
                }
                else {
                    toastr.error("Призошла ошибка при добавлении истории в избранное", "Ошибка");
                }
            });
    }
    
    function del(control) {
        send()
            .done(function(data) {
                if (data && data.success) {
                    control
                        .attr("data-state", "")
                        .attr("title", "Добавить в избранное")
                        .find("i")
                        .removeClass("glyphicon-star-empty glyphicon-star")
                        .addClass("glyphicon-star-empty");
                    toastr.success("История удалена из избранного", "Информация");
                }
                else {
                    toastr.error("Призошла ошибка при удалении истории из избранного", "Ошибка");
                }
            });
    }
    
    function checkUserLogin() {
        var needLogin = $needLogin;
        if (needLogin) {
            $("#wikids-login-modal").modal("show");
        }
        return !needLogin;
    }
    
    function favorites(control) {
        if (!checkUserLogin()) return false;
        control = $(control);
        var state = control.attr("data-state");
        if (state === "fav") {
            del(control);
        }
        else {
            add(control);
        }
    }
    
    return {
        "favorites": favorites
    }
})();
JS;
        $this->getView()->registerJs($js);
    }

}