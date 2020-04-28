<?php


namespace backend\models;


use common\models\Story;
use common\models\StorySlide;
use common\services\TransactionManager;
use Yii;
use yii\base\Model;

class SlidesOrder extends Model
{
    public $story_id;
    public $slides = [];
    public $order = [];

    protected $transactionManager;

    public function __construct($config = [])
    {
        $this->transactionManager = new TransactionManager();
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['story_id', 'integer'],
            ['story_id', 'exist', 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            ['slides', 'each', 'rule' => ['integer']],
            ['order', 'each', 'rule' => ['integer']],
        ];
    }

    public function saveSlidesOrder()
    {
        $slides = $this->slides;
        $order = $this->order;
        $this->transactionManager->wrap(function() use ($slides, $order) {
            $command = Yii::$app->db->createCommand();
            $i = 0;
            foreach ($slides as $slideID) {
                $command->update(StorySlide::tableName(), ['number' => $order[$i]], 'id = :id', [':id' => $slideID]);
                $command->execute();
                $i++;
            }
        });
    }

}