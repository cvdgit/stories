<?php

namespace backend\models;

use common\models\Story;
use common\services\TransactionManager;
use Yii;
use yii\base\Model;

class StoryEpisodeOrderForm extends Model
{

    public $order;

    private $transactionManager;

    public function __construct($config = [])
    {
        $this->transactionManager = new TransactionManager();
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['order', 'each', 'rule' => ['integer']],
        ];
    }

    public function saveEpisodeOrder(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('StoryEpisodeOrderForm is not valid');
        }
        if (count($this->order) === 0) {
            return;
        }
        $order = $this->order;
        $this->transactionManager->wrap(function() use ($order) {
            $command = Yii::$app->db->createCommand();
            foreach ($order as $episode => $storyID) {
                $command->update(Story::tableName(), ['episode' => $episode], 'id = :id', [':id' => $storyID]);
                $command->execute();
            }
        });
    }
}
