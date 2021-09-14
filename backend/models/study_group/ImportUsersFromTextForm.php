<?php

namespace backend\models\study_group;

use common\models\StudyGroupUser;
use common\services\TransactionManager;
use yii\base\Model;
use yii\helpers\Json;

class ImportUsersFromTextForm extends Model
{

    public $text;
    public $study_group_id;

    private $transactionManager;

    public function __construct($config = [])
    {
        $this->transactionManager = new TransactionManager();
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['text', 'required'],
            ['text', 'string'],
            ['study_group_id', 'integer'],
        ];
    }

    public function import(): array
    {
        if (!$this->validate()) {
            throw new \DomainException('ImportUsersFromTextForm is not valid');
        }
        $lines = explode(PHP_EOL, $this->text);
        $importData = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $form = new ImportUser();
            @[$email, $lastname, $firstname] = explode('|', $line);
            $data = ['email' => $email, 'lastname' => $lastname, 'firstname' => $firstname];
            if ($form->load($data, '')) {
                if (!$form->validate()) {
                    throw new \DomainException(Json::encode($form->errors));
                }
                $importData[] = $form;
            }
            else {
                throw new \DomainException('Model is not loaded');
            }
        }
        return $importData;
    }

    public function createGroupUsers(array $userIDs): void
    {
        $this->transactionManager->wrap(function() use ($userIDs) {
            foreach ($userIDs as $userID) {
                $model = StudyGroupUser::create($this->study_group_id, $userID);
                $model->save();
            }
        });
    }
}
