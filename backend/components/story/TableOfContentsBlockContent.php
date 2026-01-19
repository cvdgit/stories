<?php

declare(strict_types=1);

namespace backend\components\story;

use DomainException;
use phpQuery;
use yii\helpers\Html;

class TableOfContentsBlockContent
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function createFromHtml(string $html): self
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.table-of-contents')->attr('data-table-of-contents-id');
        if (empty($id)) {
            throw new DomainException('Mental Map id undefined');
        }
        return new self((string) $id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function renderWithDescription(int $slideId): string
    {
        return Html::tag('div', '', [
            'class' => 'table-of-contents',
            'data-table-of-contents-id' => $this->id,
        ]);
    }

    public function render(): string
    {
        return Html::tag('div', '', [
            'class' => 'table-of-contents',
            'data-table-of-contents-id' => $this->id,
        ]);
    }
}
