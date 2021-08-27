<?php

namespace backend\models\section;

use common\models\SiteSection;

class UpdateSectionForm extends SiteSection
{

    public function updateSection(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('UpdateSectionForm is not valid');
        }
        $this->save();
    }
}