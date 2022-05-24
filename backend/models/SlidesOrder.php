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
    public $lesson_id;
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
            [['story_id'], 'integer'],
            ['lesson_id', 'safe'],
            ['story_id', 'exist', 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            ['slides', 'each', 'rule' => ['integer']],
            ['order', 'each', 'rule' => ['integer']],
        ];
    }

    public function saveSlidesOrder(): void
    {
        $this->transactionManager->wrap(function() {

            $command = Yii::$app->db->createCommand();
            $i = 0;
            if (!empty($this->lesson_id)) {
                foreach ($this->slides as $slideID) {
                    $command->update('lesson_block', ['order' => $this->order[$i]], 'lesson_id = :lesson AND slide_id = :slide', [':lesson' => $this->lesson_id, ':slide' => $slideID]);
                    $command->execute();
                    $i++;
                }
            }
            else {

                foreach ($this->slides as $slideID) {
                    $command->update(StorySlide::tableName(), ['number' => $this->order[$i]], 'id = :id', [':id' => $slideID]);
                    $command->execute();
                    $i++;
                }
            }
        });
    }
}
