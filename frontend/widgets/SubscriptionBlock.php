<?php


namespace frontend\widgets;


use common\models\Rate;
use frontend\models\SubscriptionForm;
use yii\base\Widget;

class SubscriptionBlock extends Widget
{

    public $code;
    public $image = '';
    public $hasSubscription = false;
    public $viewName = 'subscription_block';

    public function run(): string
    {
        $rate = Rate::findRateByCode($this->code);
        $model = new SubscriptionForm();
        return $this->render($this->viewName, [
            'model' => $model,
            'rate' => $rate,
            'image' => $this->image,
            'hasSubscription' => $this->hasSubscription,
        ]);
    }
}