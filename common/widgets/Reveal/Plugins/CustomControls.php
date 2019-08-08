<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;
use yii\web\JsExpression;

class CustomControls extends AbstractPlugin implements PluginInterface
{

    public $configName = 'customControls';
    public $buttons = [];
    public $rightButtons = [];

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'controls' => $this->buttons,
                'rightControls' => $this->rightButtons,
                'controlsCallback' => new JsExpression("
	function(ev) {
	var left = $('.custom-navigate-left', $('.reveal'));
	Reveal.getProgress() === 0 ? left.attr('disabled', 'disabled') : left.removeAttr('disabled');
	var right = $('.custom-navigate-right', $('.reveal'));
	Reveal.getProgress() === 1 ? right.attr('disabled', 'disabled') : right.removeAttr('disabled');
	}
					"),
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        $dep = new Dependency('/js/player/plugins/customcontrols.css');
        return [
            $dep->src,
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/customcontrols.js'),
        ];
    }

}