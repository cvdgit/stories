<?php

namespace console\controllers;

use common\models\story\StoryStatus;
use Yii;
use yii\console\Controller;
use samdark\sitemap\Sitemap;
use yii\db\Query;

class SitemapController extends Controller
{

    public function actionCreate(): void
    {
        $sitemap = new Sitemap(Yii::getAlias('@public/sitemap.xml'));
        $urlManager = Yii::$app->urlManager;
        $sitemap->addItem($urlManager->createAbsoluteUrl(['/']), time(), Sitemap::DAILY, 1);

        $query = (new Query())
            ->select(['t.*', 't2.lft', 't2.rgt', 't2.tree'])
            ->from(['t' => '{{%site_section}}'])
            ->innerJoin(['t2' => '{{%category}}'], 't.category_id = t2.id')
            ->where('t.visible = 1');

        $storyInCategoryQuery = (new Query())
            ->from(['t3' => '{{%story_category}}'])
            ->innerJoin(['t4' => '{{%story}}'], 't4.id = t3.story_id')
            ->where('t3.category_id = t.id')
            ->andWhere('t4.status = :story_stat', [':story_stat' => StoryStatus::PUBLISHED]);
        foreach ($query->each() as $section) {

            $sectionQuery = (new Query())
                ->from(['t' => '{{%category}}'])
                ->where(['t.tree' => $section['tree']])
                ->andWhere('t.lft > :lft', [':lft' => $section['lft']])
                ->andWhere('t.rgt < :rgt', [':rgt' => $section['rgt']])
                ->andWhere(['exists', $storyInCategoryQuery])
                ->orderBy('t.name');
            $maxLastModified = 0;
            $categoryItems = [];
            foreach ($sectionQuery->each() as $category) {

                $lastModified = (new Query())
                    ->from('{{%story_category}}')
                    ->innerJoin('{{%story}}', '{{%story}}.id = {{%story_category}}.story_id')
                    ->where('{{%story_category}}.category_id = :category', [':category' => $category['id']])
                    ->andWhere('{{%story}}.status = :story_stat', [':story_stat' => StoryStatus::PUBLISHED])
                    ->max('{{%story}}.published_at');

                if ($lastModified > $maxLastModified) {
                    $maxLastModified = $lastModified;
                }
                $categoryItems[] = [
                    'location' => $urlManager->createAbsoluteUrl(['story/category', 'section' => $section['alias'], 'category' => $category['alias']]),
                    'lastModified' => $lastModified,
                    'changeFrequency' => Sitemap::WEEKLY,
                    'priority' => 0.9,
                ];

            }
            $sitemap->addItem($urlManager->createAbsoluteUrl(['story/index', 'section' => $section['alias']]), $maxLastModified, Sitemap::DAILY, 1);
            foreach ($categoryItems as $item) {
                $sitemap->addItem($item['location'], $item['lastModified'], $item['changeFrequency'], $item['priority']);
            }
        }

        $lastModified = (new Query())
            ->from('{{%story}}')
            ->where('status = :status', [':status' => StoryStatus::PUBLISHED])
            ->max('published_at');
        $sitemap->addItem($urlManager->createAbsoluteUrl(['story/bedtime-stories']), $lastModified, Sitemap::DAILY, 1);
        $sitemap->addItem($urlManager->createAbsoluteUrl(['story/audio-stories']), $lastModified, Sitemap::DAILY, 1);

        $query = (new Query())
            ->from('{{%story}}')
            ->where('status = :status', [':status' => StoryStatus::PUBLISHED])
            ->orderBy(['published_at' => SORT_DESC]);
        foreach ($query->each() as $story) {
            $sitemap->addItem($urlManager->createAbsoluteUrl(['story/view', 'alias' => $story['alias']]), $story['published_at'], Sitemap::MONTHLY, 0.9);
        }

        //$sitemap->addItem($urlManager->createAbsoluteUrl(['rate/index']), null, Sitemap::MONTHLY, 0.9);

        $lastModified = (new Query())->from('{{%news}}')->max('created_at');
        $sitemap->addItem($urlManager->createAbsoluteUrl(['news/index']), $lastModified, Sitemap::DAILY, 1);
        $query = (new Query())
            ->from('{{%news}}')
            ->where(['status' => 2])
            ->orderBy('created_at');
        foreach ($query->each() as $news) {
            $sitemap->addItem($urlManager->createAbsoluteUrl(['news/view', 'slug' => $news['slug']]), $news['updated_at'], Sitemap::MONTHLY, 0.9);
        }

        $sitemap->write();
    }
}
