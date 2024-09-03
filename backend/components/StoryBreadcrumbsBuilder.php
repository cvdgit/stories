<?php

declare(strict_types=1);

namespace backend\components;

use common\models\Story;
use Yii;

class StoryBreadcrumbsBuilder
{
    /**
     * @var Story
     */
    private $story;
    /**
     * @var string
     */
    private $title;

    public function __construct(Story $story, string $title)
    {
        $this->story = $story;
        $this->title = $title;
    }

    public function build(): array
    {
        return [
            'breadcrumbs' => [
                ['label' => 'Список историй', 'url' => ['/story/index']],
                [
                    'label' => $this->story->title,
                    'url' => Yii::$app->urlManagerFrontend->createAbsoluteUrl(
                        ['/story/view', 'alias' => $this->story->alias],
                    ),
                    'target' => '_blank',
                ],
                $this->title,
            ],
        ];
    }
}
