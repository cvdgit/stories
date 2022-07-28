<?php

namespace modules\edu\controllers;

use Ramsey\Uuid\Uuid;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Default controller for the `edu` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $cookies = $this->response->cookies;

        if ($cookies->get('uid') === null) {
            $cookies->add(new Cookie([
                'name' => 'uid',
                'value' => (string)Uuid::uuid4(),
            ]));
        }

        return $this->render('index');
    }
}
