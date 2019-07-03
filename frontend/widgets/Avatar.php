<?php


namespace frontend\widgets;


use common\models\User;
use cebe\gravatar\Gravatar;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Renders user avatar
 */
class Avatar extends Widget
{

    /**
     * @var User
     */
    public $user;
    public $size = 32;
    public $options = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $image = $this->gravatarImage();
        $profile = $this->user->profile;
        if ($profile !== null) {
            $profilePhoto = $profile->profilePhoto;
            if ($profilePhoto !== null) {
                $image = Html::img($profilePhoto->getThumbFileUrl('file', 'list'));
            }
        }
        return $image;
    }

    protected function gravatarImage()
    {
        return Gravatar::widget([
            'defaultImage' => 'identicon',
            'email' => $this->user->email ? $this->user->email : $this->user->username,
            'options' => [
                'alt' => $this->user->username,
                'width' => $this->size,
                'height' => $this->size,
            ],
            'size' => $this->size,
        ]);
    }

}