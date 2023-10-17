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

        $pattern = "/(?<=>)([^>]+)(?=<\/?)/u";
        $subject = mb_convert_encoding($request->rawBody, "UTF-8");
        $matches = [];
        preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);

        $allWords = [];
        foreach ($matches[0] as $match) {

            [$text, $offset] = $match;

            $words = str_word_count($text, 2, "<>АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя0..9{}");

            foreach ($words as $wordOffset => $word) {
                $allWords[$offset + $wordOffset] = $word;
            }
        }

        $allWords = array_filter($allWords, static function(string $word): bool {
            return $word !== "nbsp" && preg_match('/{[a-z0-9]+-[a-z0-9]+-4[a-z0-9]+-[a-z0-9]+-[a-z0-9]+}/i', $word) !== 1;
        });

        $keys = array_rand($allWords, 5);
        $keys = array_reverse($keys);

        $values = array_map(static function(int $key) use ($allWords): string {
            return mb_convert_encoding($allWords[$key] ?? "no-${key}", "UTF-8");
        }, array_values($keys));

        $fragments = [];

        foreach ($keys as $i => $start) {

            $id = Uuid::uuid4()->toString();
            $subject = substr_replace($subject, "{" . $id . "}", $start, strlen($values[$i]));

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
            "content" => $subject,
            "fragments" => $fragments,
        ];
    }
}
