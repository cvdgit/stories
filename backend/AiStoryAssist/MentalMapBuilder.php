<?php

declare(strict_types=1);

namespace backend\AiStoryAssist;

use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapPayload;
use DomainException;
use Ramsey\Uuid\UuidInterface;

class MentalMapBuilder
{
    public function createTreeMentalMap(
        UuidInterface $id,
        string $title,
        string $text,
        int $userId,
        array $fragments
    ): MentalMap {
        $payload = MentalMapPayload::treeMentalMap(
            $id,
            $title,
            $text,
            array_map(static function (array $fragment): array {
                return [
                    'id' => $fragment['id'],
                    'title' => $fragment['title'],
                ];
            }, $fragments),
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
        );

        if (!$mentalMap->save()) {
            throw new DomainException(
                'Can\'t be saved MentalMap model. Errors: ' . implode(', ', $mentalMap->getFirstErrors()),
            );
        }

        return $mentalMap;
    }

    public function createPlanMentalMap(
        UuidInterface $id,
        string $title,
        string $text,
        int $userId,
        array $fragments,
        UuidInterface $promptId = null
    ): MentalMap {
        $payload = MentalMapPayload::planMentalMap(
            $id,
            $title,
            $text,
            array_map(static function (array $fragment): array {
                return [
                    'id' => $fragment['id'],
                    'title' => $fragment['title'],
                    'description' => $fragment['description'],
                ];
            }, $fragments),
            $promptId
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
        );

        if (!$mentalMap->save()) {
            throw new DomainException(
                'Can\'t be saved MentalMap model. Errors: ' . implode(', ', $mentalMap->getFirstErrors()),
            );
        }

        return $mentalMap;
    }
}
