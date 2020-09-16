<?php

namespace backend\models\test;

use common\models\User;
use yii\base\Model;

class StudentTestModel extends Model
{

    private $user;

    public function __construct(User $user, $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }



}