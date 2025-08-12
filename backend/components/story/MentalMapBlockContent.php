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

    /**
     * @var string
     */
    private $mapType;

    public function __construct(string $id, bool $required = false, string $mapType = null)
    {
        $this->id = $id;
        $this->required = $required;
        $this->mapType = $mapType ??  'mental-map';
    }

    public static function createFromHtml(string $html): MentalMapBlockContent
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.mental-map')->attr('data-mental-map-id');
        $required = $content->find('.mental-map')->attr('data-mental-map-required') === 'true';
        $mapType = $content->find('.mental-map')->attr('data-mental-map-type');
        if (empty($id)) {
            throw new DomainException('Mental Map id undefined');
        }
        return new self((string) $id, $required, $mapType);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function renderWithDescription(int $slideId, bool $isTreeMap, string $title = 'Ментальная карта'): string
    {
        if ($isTreeMap) {
            $title .= ' (дерево)';
        }
        $link = Html::a($title, ['mental-map/editor', 'id' => $this->id, 'from_slide' => $slideId]);
        if ($this->mapType === 'mental-map-questions') {
            $link = Html::a($title, '#', ['data-mental-map-action' => 'update-questions']);
        }
        return Html::tag('div', $link, [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
            'data-mental-map-required' => var_export($this->required, true),
            'data-mental-map-type' => $this->mapType,
        ]);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
            'data-mental-map-required' => var_export($this->required, true),
            'data-mental-map-type' => $this->mapType,
        ]);
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getMapType(): string
    {
        return $this->mapType;
    }
}
