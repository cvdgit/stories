<?php


namespace frontend\widgets;


use frontend\assets\ShareAsset;
use yii\base\Widget;

class Share extends Widget
{

    public $story;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        return $this->render('share', [
            'story' => $this->story,
        ]);
    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        $view->registerJsFile('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js');
        $view->registerJsFile('//yastatic.net/share2/share.js');
        ShareAsset::register($view);
    }

}