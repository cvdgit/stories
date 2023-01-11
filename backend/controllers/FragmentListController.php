<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\MorphyWrapper;
use backend\forms\FragmentListForm;
use backend\forms\FragmentListItemForm;
use backend\services\QuestionFragmentService;
use common\rbac\UserRoles;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class FragmentListController extends Controller
{
    private $questionFragmentService;

    public function __construct($id, $module, QuestionFragmentService $questionFragmentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->questionFragmentService = $questionFragmentService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate(Request $request, Response $response, WebUser $user)
    {
        $fragmentForm = new FragmentListForm();
        $fragmentItemForm = new FragmentListItemForm();
        if ($fragmentForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$fragmentForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $items = [];
            foreach ($request->post('FragmentListItemForm') as $i => $rawModel) {
                $items[$i] = new FragmentListItemForm();
            }
            if (Model::loadMultiple($items, $request->post()) && Model::validateMultiple($items)) {
                try {
                    $this->questionFragmentService->create($user->getId(), $fragmentForm, $items);
                    return ['success' => true, 'message' => 'Список успешно создан'];
                }
                catch (\Exception $ex) {
                    return ['success' => false, 'message' => $ex->getMessage()];
                }
            }
        }
        return $this->renderAjax('create', [
            'formModel' => $fragmentForm,
            'itemFormModel'=>  $fragmentItemForm,
        ]);
    }

    public function actionSelect(): string
    {
        $lists = (new Query())
            ->select('*')
            ->from('fragment_list')
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $itemIds = array_map(static function($item) {
            return $item['id'];
        }, $lists);

        $items = (new Query())
            ->select('*')
            ->from('fragment_list_item')
            ->where(['in', 'fragment_list_id', $itemIds])
            ->orderBy([
                'name' => SORT_ASC,
                'fragment_list_id' => SORT_ASC,
            ])
            ->all();

        foreach ($lists as $i => $listItem) {
            $lists[$i]['items'] = array_filter($items, static function($item) use ($listItem) {
                return (int)$item['fragment_list_id'] === (int)$listItem['id'];
            });
        }

        return $this->renderAjax('select', [
            'items' => $lists,
        ]);
    }

    public function actionSearch(Response $response, Request $request)
    {
        $response->format = Response::FORMAT_JSON;

        $content = mb_strtolower($request->rawBody);

/*        $fragmentLists = (new Query())
            ->select([
                new Expression('DISTINCT fragment_list_tag.tag_id'),
                'listId' => 'fragment_list_tag.fragment_list_id',
                'tagName' => 'tag.name',
            ])
            ->from('fragment_list_tag')
            ->innerJoin('tag', 'fragment_list_tag.tag_id = tag.id')
            ->all();*/

        $fragmentLists = (new Query())
            ->select([
                new Expression('DISTINCT name'),
                'listId' => 'fragment_list_id',
                'tagName' => 'name',
            ])
            ->from('fragment_list_item')
            ->all();

        //$content = preg_replace('/\s+/u',' ', $content);
        //$content = trim(preg_replace('/[^\w\s\-{}]/u', '', $content));

        //$words = array_unique(preg_split('/\s/u', $content,-1,PREG_SPLIT_NO_EMPTY));

        $morphy = new MorphyWrapper();

        $result = [];
        foreach ($fragmentLists as $listItem) {

            $itemWord = $listItem['tagName'];

            $listItemBase = $morphy->getBaseForm($itemWord);
            if (empty($listItemBase)) {
                $listItemBase = $itemWord;
            }

            if (str_contains($content, mb_strtolower($listItemBase))) {

                $result[] = [
                    'word' => $itemWord,
                    'tag' => mb_strtolower($listItemBase),
                    'list_id' => $listItem['listId'],
                ];
            }
        }

/*
        $result = [];
        $debug = [];
        foreach ($words as $word) {

            if (preg_match('/{[a-z0-9]+-[a-z0-9]+-4[a-z0-9]+-[a-z0-9]+-[a-z0-9]+}/i', $word)) {
                continue;
            }

            $word = trim(preg_replace('/\W+/u', '', $word));
            if (empty($word)) {
                continue;
            }

            $matchPseudoRoot = $morphy->getPseudoRoot($word);
            $matchBaseForm = $morphy->getBaseForm($word);

            $debug[$word] = [
                'root' => $matchPseudoRoot,
                'base' => $matchBaseForm,
            ];

            $match = null;
            foreach ($fragmentLists as $listItem) {

                $tag = $listItem['tagName'];

                foreach (explode(' ', $tag) as $tagWord) {

                    if ($match) {
                        break;
                    }

                    $resultPseudoRoot = $morphy->getPseudoRoot($tagWord);
                    $resultBaseForm = $morphy->getBaseForm($tagWord);

                    $debugTag = [
                        'root' => $resultPseudoRoot,
                        'base' => $resultBaseForm,
                    ];

                    if ((!empty($matchPseudoRoot) && !empty($resultPseudoRoot)) && ($matchPseudoRoot === $resultPseudoRoot)) {
                        $match = [
                            'word' => $word,
                            'tag' => $tag,
                            'list_id' => $listItem['listId'],
                        ];
                        $debugTag['match'] = $match;
                        $debug[$word]['tags'][] = $debugTag;
                        break;
                    }

                    if ((!empty($matchBaseForm) && !empty($resultBaseForm)) && $matchBaseForm === $resultBaseForm) {
                        $match = [
                            'word' => $word,
                            'tag' => $tag,
                            'list_id' => $listItem['listId'],
                        ];
                        $debugTag['match'] = $match;
                        $debug[$word]['tags'][] = $debugTag;
                        break;
                    }

                    if (!$match) {
                        $debug[$word]['tags'][] = $debugTag;
                    }
                }
            }

            if ($match) {
                $result[] = $match;
            }
        }*/

        return ['success' => true, 'words' => $result, 'debug' => []];
    }

    public function actionSelectOne(int $list_id): string
    {
        $lists = (new Query())
            ->select('*')
            ->from('fragment_list')
            ->where(['id' => $list_id])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $itemIds = array_map(static function($item) {
            return $item['id'];
        }, $lists);

        $items = (new Query())
            ->select('*')
            ->from('fragment_list_item')
            ->where(['in', 'fragment_list_id', $itemIds])
            ->orderBy([
                'name' => SORT_ASC,
                'fragment_list_id' => SORT_ASC,
            ])
            ->all();

        foreach ($lists as $i => $listItem) {
            $lists[$i]['items'] = array_filter($items, static function($item) use ($listItem) {
                return (int)$item['fragment_list_id'] === (int)$listItem['id'];
            });
        }

        return $this->renderAjax('select_one', [
            'items' => $lists,
        ]);
    }
}
