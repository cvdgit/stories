<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyMentalMap;

use backend\MentalMap\MentalMap;
use Ramsey\Uuid\Uuid;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CopyMentalMapHandler
{
    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function handle(CopyMentalMapCommand $command): void
    {
        $mentalMap = MentalMap::findOne($command->getId()->toString());
        if ($mentalMap === null) {
            throw new NotFoundHttpException('Mental map not found');
        }
        $payload = $mentalMap->payload;
        $payload['id'] = $command->getNewId()->toString();
        $payload['name'] = $command->getName();

        if ($mentalMap->isMentalMapAsTree()) {
            $payload['treeData'] = $this->processTree($payload['treeData'], static function(array $node): array {
                $node['id'] = Uuid::uuid4()->toString();
                return $node;
            });
        } else {
            $payload['map']['images'] = array_map(static function (array $image): array {
                $image['id'] = Uuid::uuid4()->toString();
                return $image;
            }, $payload['map']['images']);
        }

        $newMentalMap = MentalMap::create(
            $command->getNewId()->toString(),
            $command->getName(),
            $payload,
            $command->getUserId(),
        );
        $newMentalMap->schedule_id = $mentalMap->schedule_id;
        $newMentalMap->map_type = $mentalMap->map_type;
        $newMentalMap->source_mental_map_id = $mentalMap->source_mental_map_id;
        if (!$newMentalMap->save()) {
            throw new BadRequestHttpException('Mental Map save exception');
        }
    }

    private function processTree(array $treeData, callable $callback): array
    {
        $new = [];
        foreach ($treeData as $node) {
            $node = $callback($node);
            if (array_key_exists('children', $node) && count($node['children']) > 0) {
                $node['children'] = $this->processTree($node['children'], $callback);
            }
            $new[] = $node;
        }
        return $new;
    }
}
