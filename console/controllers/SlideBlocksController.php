<?php

namespace console\controllers;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\services\StoryLinksService;
use common\models\StorySlide;
use yii\console\Controller;
use yii\db\Query;

class SlideBlocksController extends Controller
{

    private $storyLinksService;

    public function __construct($id, $module, StoryLinksService $storyLinksService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyLinksService = $storyLinksService;
    }

    public function actionCreateTestLinks()
    {
        $query = (new Query())
            ->select(['id', 'story_id', 'data'])
            ->from(StorySlide::tableName())
            ->where('kind = :kind', [':kind' => 0])
            ->andWhere('status = :status', [':status' => StorySlide::STATUS_VISIBLE])
            ->andWhere('data IS NOT NULL');
        foreach ($query->each() as $row) {
            $slide = (new HtmlSlideReader($row['data']))->load();
            foreach ($slide->getBlocks() as $block) {
                if ($block->isTest()) {
                    $line = 'story - ' . $row['story_id'] . '; test - ' . $block->getTestID();
                    try {
                        $this->storyLinksService->createTestLink($row['story_id'], $block->getTestID());
                        $this->stdout('[+] ' . $line . PHP_EOL);
                    }
                    catch (\Exception $ex) {
                        $this->stdout('Уже существует' . $line . PHP_EOL);
                    }
                }
            }
        }

        /*
        $query = (new Query())
            ->select(['id', 'story_id', 'data'])
            ->from(StorySlide::tableName())
            ->where('kind = :kind', [':kind' => StorySlide::KIND_QUESTION]);
        foreach ($query->each() as $row) {
            $reader = new HtmlSlideReader($row['data']);
            $slide = $reader->load();
            if ($slide->getView() === 'new-question') {
                foreach ($slide->getBlocks() as $block) {
                    if ($block->getType() === AbstractBlock::TYPE_HTML) {
                        $document = \phpQuery::newDocumentHTML($block->getContent());
                        $testID = $document->find('div.new-questions')->attr('data-test-id');
                        if (empty($testID)) {
                            $this->stdout('no test' . PHP_EOL);
                        }
                        else {
                            try {
                                $this->storyLinksService->createTestLink($row['story_id'], $testID);
                                $this->stdout('+' . PHP_EOL);
                            }
                            catch (\Exception $ex) {
                                $this->stdout('Уже существует' . PHP_EOL);
                            }
                        }
                    }
                }
            }
        }
        */

        $this->stdout('Done!' . PHP_EOL);
    }

}