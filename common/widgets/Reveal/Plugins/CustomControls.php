<?php


namespace common\widgets\Reveal\Plugins;


use common\widgets\Reveal\Dependency;
use yii\web\JsExpression;

class CustomControls extends AbstractPlugin implements PluginInterface
{

    public $configName = 'customControls';
    public $buttons = [];

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'controls' => $this->buttons,
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
        return [
            '/js/player/plugins/customcontrols.css',
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/customcontrols.js'),
        ];
    }

}