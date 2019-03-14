<?php

namespace frontend\components;

use yii;
use yii\base\Behavior;

class SeoViewBehavior extends Behavior
{

	protected $title = '';
	protected $metaDescription = '';
	protected $metaKeywords = '';
	protected $h1 = '';

	public function setMetaTags($title, $description = '', $keywords = '', $h1 = '')
	{
		$this->title = $title . ' | ' . Yii::$app->name;
		$this->metaDescription = $description;
		$this->metaKeywords = $keywords;
		$this->h1 = $h1;
		
		$this->registerMetaTags();
	}

	protected function registerMetaTags()
	{
		 /* @var $view View */
        $view = $this->owner;

        $view->title = $this->title;
        
        if ($this->h1 == '') {
        	$this->h1 = $this->title;
        }
        $view->params['h1'] = $this->h1;

		if (!empty($this->metaDescription)) {
			$view->registerMetaTag(['name' => 'description', 'content' => $this->metaDescription]);
		}

		if (!empty($this->metaKeywords)) {
			$view->registerMetaTag(['name' => 'keywords', 'content' => $this->metaKeywords]);
		}
	}

	public function getHeader()
	{
		 /* @var $view View */
        $view = $this->owner;
		return isset($view->params['h1']) ? $view->params['h1'] : '';
	}

}