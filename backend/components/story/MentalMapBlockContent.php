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
     * @var int
     */
    private $slideId;

    public function __construct(string $id, int $slideId)
    {
        $this->id = $id;
        $this->slideId = $slideId;
    }

    public static function createFromHtml(string $html, int $slideId): MentalMapBlockContent
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.mental-map')->attr('data-mental-map-id');
        if (empty($id)) {
            throw new DomainException('Mental Map id undefined');
        }
        return new self((string) $id, $slideId);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function renderWithDescription(): string
    {
        $link = Html::a('Ментальная карта', ['mental-map/editor', 'id' => $this->id, 'from_slide' => $this->slideId]);
        return Html::tag('div', $link, [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
        ]);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'mental-map',
            'data-mental-map-id' => $this->id,
        ]);
    }
}
