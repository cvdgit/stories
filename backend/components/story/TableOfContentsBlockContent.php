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
    /**
     * @var string
     */
    private $payload;

    public function __construct(string $id, string $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    public static function createFromHtml(string $html): self
    {
        $content = phpQuery::newDocumentHTML($html);
        $id = $content->find('.table-of-contents')->attr('data-table-of-contents-id');
        if (empty($id)) {
            throw new DomainException('Mental Map id undefined');
        }
        $payload = $content->find('.table-of-contents .table-of-contents-payload')->text();
        return new self((string) $id, $payload);
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
        return Html::tag(
            'div',
            '<script class="table-of-contents-payload" type="application/json">' . $this->payload . '</script>',
            [
                'class' => 'table-of-contents',
                'data-table-of-contents-id' => $this->id,
            ],
        );
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
