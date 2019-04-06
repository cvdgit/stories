<?php

namespace frontend\widgets;

use yii\base\Widget;
use frontend\models\ContactForm;

class ContactWidget extends Widget
{

	public function init()
	{
		parent::init();
	}

	public function run()
	{
		$model = new ContactForm();
		return $this->render('contact', [
			'model' => $model,
		]);
	}

}