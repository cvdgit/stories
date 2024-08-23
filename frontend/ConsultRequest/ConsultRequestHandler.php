<?php

declare(strict_types=1);

namespace frontend\ConsultRequest;

use common\models\ContactRequest;
use DomainException;

class ConsultRequestHandler
{
    public function handle(ConsultRequestCommand $command): void
    {
        $model = ContactRequest::create($command->getName(), $command->getPhone(), $command->getEmail(), 'Заявка с формы консультации');
        if (!$model->save()) {
            throw new DomainException('Consult request save exception');
        }
    }
}
