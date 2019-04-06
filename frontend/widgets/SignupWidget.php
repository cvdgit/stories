<?php

namespace frontend\widgets;

use yii\base\Widget;
use frontend\models\SignupForm;

class SignupWidget extends Widget
{

	public function init()
	{
		parent::init();
	}

	public function run()
	{
		$model = new SignupForm();
		return $this->render('signup', [
			'model' => $model,
		]);
	}

}