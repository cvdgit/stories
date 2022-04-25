<?php

namespace backend\components\editor;

use yii\helpers\Json;
use yii\helpers\Url;

class EditorConfig
{

    private $config = [];

    public function setValue(string $name, $value = null): self
    {
        $this->config[$name] = $value;
        return $this;
    }

    public function setUrlValue(string $name, $url): self
    {
        return $this->setValue($name, Url::to($url));
    }

    public function setUrlValues(array $values): self
    {
        foreach ($values as $name => $url) {
            $this->setValue($name, Url::to($url));
        }
        return $this;
    }

    public function asJson(): string
    {
        return Json::htmlEncode($this->config);
    }
}
