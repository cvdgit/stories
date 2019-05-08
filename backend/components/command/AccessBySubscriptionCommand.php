<?php


namespace backend\components\command;


use common\models\Story;

class AccessBySubscriptionCommand
{

    protected $storyIDs;

    public function __construct($storyIDs)
    {
        $this->storyIDs = $storyIDs;
    }

    public function execute()
    {
        foreach ($this->storyIDs as $storyID) {
            $model = Story::findModel($storyID);
            $model->sub_access = 1;
            $model->save(false, ['sub_access']);
        }
    }

}