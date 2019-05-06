<?php


namespace common\services;


use common\models\User;
use frontend\models\ProfileEditForm;

class ProfileService
{

    public function update(User $user, ProfileEditForm $form): void
    {
        $profile = $user->updateProfile(
            $form->first_name,
            $form->last_name
        );
        $profile->addProfilePhoto($form->photoForm->file);
        $profile->save();
    }

}