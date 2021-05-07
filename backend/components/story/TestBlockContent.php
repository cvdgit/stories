<?php

namespace backend\components\story;

use yii\helpers\Html;

class TestBlockContent
{

    /** @var int */
    private $testID;
    /** @var false|mixed */
    private $required;

    public function __construct(int $testID, $required = 0)
    {
        $this->testID = $testID;
        $this->required = $required;
    }

    public static function createFromHtml(string $html): self
    {
        $content = pq($html)->find('div.new-questions');
        return new self($content->attr('data-test-id'), $content->attr('data-test-required') ?? false);
    }

    public function render(array $params = []): string
    {
        $options = [
            'class' => 'new-questions',
            'data-test-id' => $this->testID,
            'data-test-required' => $this->required,
        ];
        foreach ($params as $paramName => $paramValue) {
            $options['data-' . $paramName] = $paramValue;
        }
        return Html::tag('div', '', $options);
    }

    /**
     * @return int
     */
    public function getTestID(): int
    {
        return $this->testID;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

}