<?php

namespace common\widgets;

use Yii;
use yii\base\Widget;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;

class RevealWidget extends Widget
{

    public $id;
	public $data;
	public $storyId;
    public $options = [];
    public $initializeReveal = true;
    public $canViewStory = false;

    public $assets = [];
    public $plugins = [];

    protected $config = [
        'width' => 1280,
        'height' => 720,
        'margin' => 0.01,
        'transition' => 'none',
        'backgroundTransition' => 'slide',
        'center' => false,
        'controls' => false,
        'controlsLayout' => 'bottom-right',
        'controlsBackArrows' => 'faded',
        'controlsTutorial' => false,
        'progress' => true,
        'history' => true,
        'mouseWheel' => false,
        'showNotes' => false,
        'slideNumber' => false,
        'shuffle' => false,
        'loop' => false,
        'hash' => true,
        'hashOneBasedIndex' => true,
        'rtl' => false,
        'help' => false,
        'dependencies' => [],
        'touch' => true,
        'embedded' => true,
        'maxScale' => 1.0,
    ];

    protected $defaultAssets = [];

    public function run()
	{
        if (empty($this->data)) {
            $this->data = Html::tag('div', '', ['class' => 'slides']);
        }
        echo Html::tag('div', $this->data, ['class' => 'reveal', 'id' => $this->id]);
        $this->registerClientScript();
	}

    public function registerClientScript(): void
    {

        foreach ($this->options as $optionName => $optionValue) {
            if (isset($this->config[$optionName])) {
                $this->config[$optionName] = $optionValue;
            }
        }

        $this->registerPlugins();

        $configJson = Json::htmlEncode($this->config);
        $js = new JsExpression("window.WikidsRevealConfig = $configJson;");

        $view = $this->getView();
        $view->registerJs($js, View::POS_HEAD);

        $this->registerAssets();

        if ($this->initializeReveal) {
            $js = 'Reveal.initialize(WikidsRevealConfig);';
            $view->registerJs($js, View::POS_END);
        }
    }

    protected function registerPlugins(): void
    {
        $view = $this->getView();
        foreach ($this->plugins as $params) {
            $plugin = Yii::createObject($params);
            $this->config['dependencies'] = array_merge($this->config['dependencies'], $plugin->dependencies());
            $this->config += $plugin->pluginConfig();
            foreach ($plugin->pluginCssFiles() as $cssFile) {
                $view->registerCssFile($cssFile, ['depends' => $this->defaultAssets[0]]);
            }
        }
    }

    protected function registerAssets(): void
    {
        $view = $this->getView();
        foreach (array_merge($this->defaultAssets, $this->assets) as $assetClass) {
            $assetClass::register($view);
        }
    }

}
