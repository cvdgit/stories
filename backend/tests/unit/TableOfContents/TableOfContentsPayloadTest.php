<?php

declare(strict_types=1);

namespace backend\tests\unit\TableOfContents;

use backend\TableOfContents\TableOfContentsGroup;
use backend\TableOfContents\TableOfContentsPayload;
use Codeception\Test\Unit;
use Ramsey\Uuid\Uuid;
use yii\helpers\Json;

class TableOfContentsPayloadTest extends Unit
{
    public function testFromPayload(): void
    {
        $payload = [
            "title" => "Оглавление",
            "groups" => [
                [
                    "id" => "fb2061d1-4eba-43a5-9668-dd2e691d7b4e",
                    "name" => "Андерс Линдеберг и борьба за свободу слова",
                    "slides" => [
                        [
                            "id" => 1097,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d",
                        ],
                        [
                            "id" => 1098,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "bbe63234-6b51-473a-8480-92be5316caed",
                        ],
                        [
                            "id" => 1099,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                        ],
                        [
                            "id" => 1100,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "9c317a70-e0d4-4549-8271-5b404075ee73",
                        ],
                    ],
                    "cards" => [
                        ["id" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d", "name" => "Сомнения в королевской власти"],
                        ["id" => "bbe63234-6b51-473a-8480-92be5316caed", "name" => "Приговор и отказ от помилования"],
                        [
                            "id" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                            "name" => "Упрямство Линдеберга и попытки тюремщиков",
                        ],
                        ["id" => "9c317a70-e0d4-4549-8271-5b404075ee73", "name" => "Победа над системой"],
                    ],
                ],
            ],
        ];
        $tableOfContents = TableOfContentsPayload::fromPayload($payload);
        $this->assertEquals(
            Json::encode($payload),
            Json::encode($tableOfContents),
        );
    }

    public function testAddGroup(): void
    {
        $payload = [
            'title' => 'Table Of Contents',
            'groups' => [],
        ];
        $tableOfContents = TableOfContentsPayload::fromPayload($payload);
        $tableOfContents->addGroup(
            new TableOfContentsGroup(
                $uuid = Uuid::uuid4(),
                'New Group',
            ),
        );
        $this->assertEquals(
            Json::encode([
                'title' => 'Table Of Contents',
                'groups' => [
                    ['id' => $uuid, 'name' => 'New Group', 'slides' => [], 'cards' => []],
                ],
            ]),
            Json::encode($tableOfContents),
        );
    }

    public function testSetCardSlides(): void
    {
        $payload = [
            "title" => "Оглавление",
            "groups" => [
                [
                    "id" => "fb2061d1-4eba-43a5-9668-dd2e691d7b4e",
                    "name" => "Андерс Линдеберг и борьба за свободу слова",
                    "slides" => [
                        [
                            "id" => 1097,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d",
                        ],
                        [
                            "id" => 1098,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "bbe63234-6b51-473a-8480-92be5316caed",
                        ],
                        [
                            "id" => 1099,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                        ],
                        [
                            "id" => 1100,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "9c317a70-e0d4-4549-8271-5b404075ee73",
                        ],
                    ],
                    "cards" => [
                        ["id" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d", "name" => "Сомнения в королевской власти"],
                        ["id" => "bbe63234-6b51-473a-8480-92be5316caed", "name" => "Приговор и отказ от помилования"],
                        [
                            "id" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                            "name" => "Упрямство Линдеберга и попытки тюремщиков",
                        ],
                        ["id" => "9c317a70-e0d4-4549-8271-5b404075ee73", "name" => "Победа над системой"],
                    ],
                ],
            ],
        ];
        $tableOfContents = TableOfContentsPayload::fromPayload($payload);
        $tableOfContents->setCardSlides(1098, [111, 222]);

        $expectedPayload = [
            "title" => "Оглавление",
            "groups" => [
                [
                    "id" => "fb2061d1-4eba-43a5-9668-dd2e691d7b4e",
                    "name" => "Андерс Линдеберг и борьба за свободу слова",
                    "slides" => [
                        [
                            "id" => 1097,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d",
                        ],
                        [
                            "id" => 1099,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                        ],
                        [
                            "id" => 1100,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "9c317a70-e0d4-4549-8271-5b404075ee73",
                        ],
                        [
                            "id" => 111,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "bbe63234-6b51-473a-8480-92be5316caed",
                        ],
                        [
                            "id" => 222,
                            "title" => "",
                            "slideNumber" => 1,
                            "cardId" => "bbe63234-6b51-473a-8480-92be5316caed",
                        ],
                    ],
                    "cards" => [
                        ["id" => "8421e7b9-2f4c-4aa8-a1cf-b91a3213679d", "name" => "Сомнения в королевской власти"],
                        ["id" => "bbe63234-6b51-473a-8480-92be5316caed", "name" => "Приговор и отказ от помилования"],
                        [
                            "id" => "18a4ecd6-094e-4ca7-b889-62e595f95208",
                            "name" => "Упрямство Линдеберга и попытки тюремщиков",
                        ],
                        ["id" => "9c317a70-e0d4-4549-8271-5b404075ee73", "name" => "Победа над системой"],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            Json::encode($expectedPayload),
            Json::encode($tableOfContents),
        );
    }
}
