<?php


namespace console\controllers;


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

        $lastModified = (new Query())->from('{{%story}}')->max('created_at');
        $sitemap->addItem($urlManager->createAbsoluteUrl(['story/index']), $lastModified, Sitemap::DAILY, 1);

        $query = (new Query())
            ->from('{{%story}}')
            ->where(['status' => 1])
            ->orderBy('created_at');
        foreach ($query->each() as $story) {
            $sitemap->addItem($urlManager->createAbsoluteUrl(['story/view', 'alias' => $story['alias']]), $story['updated_at'], Sitemap::MONTHLY, 0.9);
        }

        $sitemap->addItem($urlManager->createAbsoluteUrl(['rate/index']), null, Sitemap::MONTHLY, 0.9);

        $query = (new Query())
            ->from('{{%category}}')
            ->where(['tree' => 0])
            ->andWhere('depth > 0')
            ->orderBy('name');
        foreach ($query->each() as $category) {
            $lastModified = (new Query())->from('{{%story}}')->where(['category_id' => $category['id']])->max('created_at');
            if ($lastModified === null) {
                $lastModified = time();
            }
            $sitemap->addItem($urlManager->createAbsoluteUrl(['story/category', 'category' => $category['alias']]), $lastModified, Sitemap::WEEKLY, 0.9);
        }

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