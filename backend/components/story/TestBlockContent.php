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
        $content = \phpQuery::newDocumentHTML($html);
        if ($content->attr('data-test-id') === null) {
            $content = $content->find('.new-questions');
        }
        return new self($content->attr('data-test-id'), $content->attr('data-test-required') ?? false);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'new-questions',
            'data-test-id' => $this->testID,
            'data-test-required' => $this->required,
        ]);
    }

    public function renderWithDescription(array $params = [], string $content = ''): string
    {
        $options = [
            'class' => 'new-questions',
            'data-test-id' => $this->testID,
            'data-test-required' => $this->required,
        ];
        foreach ($params as $paramName => $paramValue) {
            $options['data-' . $paramName] = $paramValue;
        }

        $link = Html::a($content, ["/test/update", "id" => $this->testID], ["data-toggle" => "tooltip", "title" => "Редактировать тест", "target" => "_blank", "class" => "goto-test"]);
        return Html::tag('div', $link, $options);
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
