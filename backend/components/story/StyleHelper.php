<?php

declare(strict_types=1);

namespace backend\components\story;

class StyleHelper {
    public static function styleToArray(string $style): array
    {
        $styleArray = [];
        foreach (explode(';', $style) as $part) {
            if (!empty($part)) {
                [$paramName, $paramValue] = explode(':', $part);
                $styleArray[trim($paramName)] = trim($paramValue);
            }
        }
        return $styleArray;
    }

    public static function arrayToStyle(array $styleArray): string
    {
        $style = '';
        foreach ($styleArray as $param => $value) {
            $style .= "$param: $value;";
        }
        return $style;
    }

    public static function setStyleValue(string $style, string $param, string $value): string
    {
        $styleArray = self::styleToArray($style);
        $styleArray[$param] = $value;
        return self::arrayToStyle($styleArray);
    }
}
