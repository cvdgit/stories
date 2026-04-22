<?php

declare(strict_types=1);

namespace backend\AiStoryAssist;

use backend\MentalMap\MentalMap;
use backend\MentalMap\MentalMapPayload;
use backend\MentalMap\MentalMapPayloadImage;
use DomainException;
use Ramsey\Uuid\UuidInterface;

class MentalMapBuilder
{
    public function createMentalMap(
        UuidInterface $id,
        string $title,
        string $text,
        int $userId,
        ?MentalMapPayloadImage $mapImage = null
    ): MentalMap {
        $payload = MentalMapPayload::mentalMap(
            $id,
            $title,
            $text,
            $mapImage,
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
            MentalMap::TYPE_MENTAL_MAP,
        );

        if (!$mentalMap->save()) {
            throw new DomainException(
                'Can\'t be saved MentalMap model. Errors: ' . implode(', ', $mentalMap->getFirstErrors()),
            );
        }

        return $mentalMap;
    }

    public function createTreeMentalMap(
        UuidInterface $id,
        string $title,
        string $text,
        int $userId,
        array $fragments,
        string $type = MentalMap::TYPE_MENTAL_MAP
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
            }, MentalMapPayload::filterEmptyFragments($fragments)),
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
            $type,
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
        UuidInterface $promptId = null,
        string $type = MentalMap::TYPE_MENTAL_MAP_PLAN
    ): MentalMap {
        $fragments = MentalMapPayload::filterEmptyFragments($fragments);
        if ($type === MentalMap::TYPE_MENTAL_MAP_PLAN_ACCUMULATION) {
            $fragments = MentalMapPayload::accumulateFragments($fragments);
        } else {
            $fragments = array_map(static function (array $fragment): array {
                return [
                    'id' => $fragment['id'],
                    'title' => $fragment['title'],
                    'description' => $fragment['description'],
                ];
            }, $fragments);
        }

        $payload = MentalMapPayload::planMentalMap(
            $id,
            $title,
            $text,
            $fragments,
            $promptId,
            $type === MentalMap::TYPE_MENTAL_MAP_PLAN_ACCUMULATION,
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
            $type,
        );

        if (!$mentalMap->save()) {
            throw new DomainException(
                'Can\'t be saved MentalMap model. Errors: ' . implode(', ', $mentalMap->getFirstErrors()),
            );
        }

        return $mentalMap;
    }

    public function createTreeDialogMentalMap(
        UuidInterface $id,
        string $title,
        string $text,
        int $userId,
        array $fragments,
        string $type = MentalMap::TYPE_MENTAL_MAP_TREE_DIALOG
    ): MentalMap {
        $payload = MentalMapPayload::treeDialogMentalMap(
            $id,
            $title,
            $text,
            array_map(static function (array $fragment): array {
                return [
                    'id' => $fragment['id'],
                    'title' => $fragment['title'],
                    'description' => $fragment['description'],
                ];
            }, MentalMapPayload::filterEmptyFragments($fragments)),
        );

        $mentalMap = MentalMap::createFromPayload(
            $id->toString(),
            $payload,
            $userId,
            $type,
        );

        if (!$mentalMap->save()) {
            throw new DomainException(
                'Can\'t be saved MentalMap model. Errors: ' . implode(', ', $mentalMap->getFirstErrors()),
            );
        }

        return $mentalMap;
    }
}
