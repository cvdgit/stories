<?php

namespace frontend\components;

use Yii;
use yii\web\Request;

class StorySorter extends \yii\data\Sort
{

	public function getCurrentOrderName(): string
	{
		$request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];
        if (isset($params[$this->sortParam])) {
    	    foreach ($this->parseSortParam($params[$this->sortParam]) as $attribute) {
                if (strncmp($attribute, '-', 1) === 0) {
                    $attribute = substr($attribute, 1);
                }
                if (isset($this->attributes[$attribute])) {
        	        return $this->attributes[$attribute]['label'];
                }
    	    }
        }
        else {
    	    if (is_array($this->defaultOrder)) {
                $order = array_keys($this->defaultOrder);
      	        $attribute = array_pop($order);
                if (isset($this->attributes[$attribute])) {
        	        return $this->attributes[$attribute]['label'];
                }
            }
        }
        return '';
	}

}