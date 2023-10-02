<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Fragment;

use yii\base\Action;

class FragmentAction extends Action
{
    public function run()
    {
        $itemForm = new FragmentItemForm();

        return $this->controller->renderAjax('fragment', [
            'itemFormModel' => $itemForm,
        ]);
    }
}
