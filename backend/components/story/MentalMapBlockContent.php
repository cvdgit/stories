<?php

declare(strict_types=1);

namespace backend\components\story;

use DomainException;
use phpQuery;
use yii\helpers\Html;

class MentalMapBlockContent
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

    public static function createFromHtml(string $html): MentalMapBlockContent
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.mental-map')->attr('data-mental-map-id');
        $required = $content->find('.mental-map')->attr('data-mental-map-required') === 'true';
        if (empty($id)) {
            throw new DomainException('Mental Map id undefined');
        }
        return new self((string) $id, $required);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function renderWithDescription(int $slideId): string
    {
        $link = Html::a('Ментальная карта', ['mental-map/editor', 'id' => $this->id, 'from_slide' => $slideId]);
        return Html::tag('div', $link, [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
            'data-mental-map-required' => var_export($this->required, true),
        ]);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
            'data-mental-map-required' => var_export($this->required, true),
        ]);
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
