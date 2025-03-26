<?php

declare(strict_types=1);

namespace backend\components\story;

use DomainException;
use phpQuery;
use yii\helpers\Html;

class RetellingBlockContent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $required;

    public function __construct(string $id, bool $required = false)
    {
        $this->id = $id;
        $this->required = $required;
    }

    public static function createFromHtml(string $html): RetellingBlockContent
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.retelling-block')->attr('data-retelling-id');
        $required = $content->find('.retelling-block')->attr('data-retelling-required') === 'true';
        if (empty($id)) {
            throw new DomainException('Retelling id undefined');
        }
        return new self((string) $id, $required);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function renderWithDescription(int $slideId, string $title = 'Пересказ'): string
    {
        $link = Html::a($title, '#', ['data-retelling-action' => 'update']);
        return Html::tag('div', $link, [
            'class' => 'retelling-block',
            'data-retelling-id' => $this->id,
            'data-retelling-required' => var_export($this->required, true),
        ]);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'retelling-block',
            'data-retelling-id' => $this->id,
            'data-retelling-required' => var_export($this->required, true),
        ]);
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
