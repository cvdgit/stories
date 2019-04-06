<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\models\LoginForm;

class LoginWidget extends Widget
{

	public function init()
	{
		parent::init();
	}

	public function run()
	{
		$model = new LoginForm();
		return $this->render('login', [
			'model' => $model,
		]);
	}

}