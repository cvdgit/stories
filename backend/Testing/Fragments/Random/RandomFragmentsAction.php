<?php

declare(strict_types=1);

namespace backend\Testing\Fragments\Random;

use Ramsey\Uuid\Uuid;
use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class RandomFragmentsAction extends Action
{
    public function run(Response $response, Request $request): array
    {
        $response->format = Response::FORMAT_JSON;

        $content = mb_convert_encoding($request->rawBody, "UTF-8");
        $words = str_word_count($content, 2, "АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя0..9{}");

        $words = array_filter($words, static function(string $word): bool {
            return preg_match('/{[a-z0-9]+-[a-z0-9]+-4[a-z0-9]+-[a-z0-9]+-[a-z0-9]+}/i', $word) !== 1;
        });

        //die(print_r($words));

        $keys = array_rand($words, 5);
        $keys = array_reverse($keys);

        $values = array_map(static function(int $key) use ($words): string {
            return mb_convert_encoding($words[$key] ?? "no-${key}", "UTF-8");
        }, array_values($keys));

        $fragments = [];

        foreach ($keys as $i => $start) {

            $id = Uuid::uuid4()->toString();
            $content = substr_replace($content, "{" . $id . "}", $start, strlen($values[$i]));

            $fragments[] = [
                "start" => $start,
                "id" => $id,
                "word" => $values[$i],
                "items" => [
                    [
                        "id" => Uuid::uuid4()->toString(),
                        "correct" => true,
                        "title" => $values[$i],
                        "order" => 1,
                    ],
                ],
            ];
        }

        return [
            "success" => true,
            "content" => $content,
            "fragments" => $fragments,
        ];
    }
}
