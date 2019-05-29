<?php


namespace backend\components\story\writer\HTML;


use backend\components\story\AbstractBlock;
use backend\components\story\writer\HTML\elements\AbstractElement;

class AbstractMarkup
{

    protected $block;
    protected $element;

    public function __construct(AbstractBlock $block, AbstractElement $element)
    {
        $this->block = $block;
        $this->element = $element;
    }

    public function styleToArray($style): array
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

    public function arrayToStyle($styleArray): string
    {
        $style = '';
        foreach ($styleArray as $param => $value) {
            $style .= "{$param}: {$value};";
        }
        return $style;
    }

    /**
     * @return AbstractElement
     */
    public function getElement(): AbstractElement
    {
        return $this->element;
    }

    /**
     * @return AbstractBlock
     */
    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function setStyleValue(string $style, string $param, string $value): string
    {
        $styleArray = $this->styleToArray($style);
        $styleArray[$param] = $value;
        return $this->arrayToStyle($styleArray);
    }
/*
       public function getStyleValue($param): string
       {
           $value = '';
           if (isset($this->attributes['style'])) {
               $styleArray = $this->styleToArray($this->attributes['style']);
               $value = isset($styleArray[$param]) ? $styleArray[$param] : '';
           }
           return $value;
       }*/

}