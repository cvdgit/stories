<?php

namespace backend\models\section;

use common\models\SiteSection;

class CreateSectionForm extends SiteSection
{

    public function createSection(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('CreateSectionForm is not valid');
        }
        $this->save();
    }
}