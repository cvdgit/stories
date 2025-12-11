<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\components\SlideModifier;
use backend\models\StoryAccessByLinkForm;
use backend\models\StoryBatchCommandForm;
use backend\models\StoryEpisodeOrderForm;
use backend\models\WordListFromStoryForm;
use backend\services\ImageService;
use backend\services\StoryEditorService;
use common\components\StoryCover;
use common\models\story\StoryStatus;
use common\services\TransactionManager;
use DomainException;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use common\models\Story;
use backend\models\StorySearch;
use common\services\StoryService;
use common\rbac\UserRoles;
use backend\models\StoryCoverUploadForm;
use backend\models\StoryFileUploadForm;
use backend\models\SourcePowerPointForm;

class StoryController extends BaseController
{

    public $service;
    protected $editorService;
    /**
     * @var ImageService
     */
    private $imageService;
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function __construct($id,
                                $module,
                                StoryService $service,
                                StoryEditorService $editorService,
                                ImageService $imageService,
                                TransactionManager $transactionManager,
                                $config = [])
    {
        $this->service = $service;
        $this->editorService = $editorService;
        parent::__construct($id, $module, $config);
        $this->imageService = $imageService;
        $this->transactionManager = $transactionManager;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new Story model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Story();
        $model->user_id = Yii::$app->user->getId();
        $model->loadDefaultValues();
        $model->source_id = Story::SOURCE_POWERPOINT;
        $model->category_id = 1;

        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                $model->cover = $coverUploadForm->upload($model->cover);
            }

            $fileUploadForm->uploadFile($model);

            $model->categories = explode(',', $model->story_categories);
            if ($model->story_playlists) {
                $model->playlists = explode(',', $model->story_playlists);
            }

            $model->save(false);
            return $this->redirect(['update', 'id' => $model->id]);
        }

        foreach ($model->getErrors() as $error) {
            Yii::$app->session->setFlash('error', $error);
        }

        return $this->render('create', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
            'fileUploadForm' => $fileUploadForm,
        ]);
    }

    public function actionIndex(int $status = StoryStatus::DRAFT)
    {
        $searchModel = new StorySearch();
        $storyStatus = new StoryStatus($status);
        if ($storyStatus->isPublished()) {
            $searchModel->defaultSortField = 'published_at';
            $searchModel->defaultSortOrder = SORT_DESC;
        }
        $searchModel->status = $storyStatus->getStatus();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'status' => $storyStatus,
        ]);
    }

    /**
     * Updates an existing Story model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel(Story::class, $id);
        $model->fillStoryCategories();
        $model->fillStoryPlaylists();

        if ($model->source_id == Story::SOURCE_SLIDESCOM) {
            $model->source_dropbox = $model->story_file;
        }
        if ($model->source_id == Story::SOURCE_POWERPOINT) {
            $model->source_powerpoint = $model->story_file;
        }

        $coverUploadForm = new StoryCoverUploadForm();
        $fileUploadForm = new StoryFileUploadForm();

        $powerPointForm = new SourcePowerPointForm();
        $powerPointForm->storyId = $model->id;
        $powerPointForm->storyFile = $model->story_file;
        $powerPointForm->slidesNumber = $model->slides_number;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $coverUploadForm->coverFile = UploadedFile::getInstance($coverUploadForm, 'coverFile');
            if ($coverUploadForm->coverFile !== null) {
                $model->cover = $coverUploadForm->upload($model->cover);
            }

            $fileUploadForm->uploadFile($model);

            $model->categories = explode(',', $model->story_categories);

            if ($model->story_playlists) {
                $model->playlists = explode(',', $model->story_playlists);
            }

            $model->save(false);
            Yii::$app->session->setFlash('success', 'Изменения успешно сохранены');

            //$powerPointForm->storyFile = $model->story_file;
            return $this->refresh();
        }

        $wordListModel = new WordListFromStoryForm();
        $wordListModel->story_id = $model->id;

        Yii::$app->getUser()->setReturnUrl(Url::canonical());

        return $this->render('update', [
            'model' => $model,
            'coverUploadForm' => $coverUploadForm,
            'fileUploadForm' => $fileUploadForm,
            'powerPointForm' => $powerPointForm,
            'wordListModel' => $wordListModel,
        ]);
    }

    /**
     * Deletes an existing Story model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel(Story::class, $id);
        $this->service->deleteStoryFiles($model);
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionImportFromPowerPoint()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SourcePowerPointForm();
        if ($model->load(Yii::$app->request->post())) {
            $storyModel = $this->findModel(Story::class, $model->storyId);
            $this->service->importStoryFromPowerPoint($storyModel);
        }
        return ['success' => true];
    }

    public function actionDownload($id)
    {
        try {
            $model = $this->findModel(Story::class, $id);
            $file = $model->getStoryFilePath();
            if (file_exists($file)) {
                Yii::$app->response->sendFile($file);
            }
        }
        catch (Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
            return $this->goBack();
        }
    }

    public function actionBatch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new StoryBatchCommandForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->command === 'AccessBySubscription') {
                Yii::$app->db
                    ->createCommand()
                    ->update('{{%story}}', ['sub_access' => 1],'id IN (' . $model->story_ids . ')')
                    ->execute();
            }

            if ($model->command === 'AccessFree') {
                Yii::$app->db
                    ->createCommand()
                    ->update('{{%story}}', ['sub_access' => 0],'id IN (' . $model->story_ids . ')')
                    ->execute();
            }

            return ['success' => true];
        }
        return ['success' => false];
    }

    public function actionPublish($id)
    {
        $sendEmail = (int) Yii::$app->request->post('sendNotification') === 1;
        $model = Story::findModel($id);
        try {
            $this->service->publishStory($model, $sendEmail);
            $message = 'История опубликована';
            if ($model->isForPublication()) {
                $message = 'История отправлена на публикацию';
            }
            Yii::$app->session->setFlash('success', $message);
        }
        catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Ошибка публикации: ' . $e->getMessage());
        }
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionUnpublish($id)
    {
        $model = Story::findModel($id);
        $this->service->unPublishStory($model);
        Yii::$app->session->setFlash('success', 'История снята с публикации');
        return $this->redirect(['update', 'id' => $model->id]);
    }

    /**
     * @throws RangeNotSatisfiableHttpException|NotFoundHttpException|InvalidConfigException
     */
    public function actionText(int $id): void
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $story = $this->findModel(Story::class, $id);
        $text = $this->editorService->textFromStory(
            SlideModifier::slidesToHTML($story->getSlidesWithLinkData()),
        );
        Yii::$app->response->sendContentAsFile($text, $story->alias. '.txt');
    }

    public function actionCancelPublication($id)
    {
        $model = $this->findModel(Story::class, $id);
        try {
            $model->cancelPublication();
            Yii::$app->session->setFlash('success', 'Публикация отменена');
        }
        catch (Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionGrantAccessByLink(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Story $storyModel */
        $storyModel = $this->findModel(Story::class, $id);
        $storyModel->grantLinkAccess();
        $accessForm = new StoryAccessByLinkForm($storyModel);
        return ['success' => true, 'accessLink' => $accessForm->access_link];
    }

    public function actionRevokeAccessByLink(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Story $storyModel */
        $storyModel = $this->findModel(Story::class, $id);
        $storyModel->revokeLinkAccess();
        return ['success' => true];
    }

    public function actionSaveEpisodeOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new StoryEpisodeOrderForm();
        if ($form->load(Yii::$app->request->post())) {
            $form->saveEpisodeOrder();
        }
        return ['success' => true];
    }

    /**
     * @throws NotFoundHttpException
     * @throws \JsonException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionJson(int $id, Response $response): void
    {
        $response->format = Response::FORMAT_JSON;
        $storyModel = Story::findOne($id);
        if ($storyModel === null) {
            throw new NotFoundHttpException("История не найдена");
        }
        $json = $this->editorService->jsonFromStory($storyModel->slidesData(true), Yii::$app->urlManagerFrontend->createAbsoluteUrl(['preview/view', 'alias' => $storyModel->alias]), $storyModel->title);
        $response->sendContentAsFile(json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $storyModel->alias. '.json');
    }

    /**
     * @throws NotFoundHttpException
     * @throws \JsonException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionExportStories(Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $storyIds = [297, 298, 308, 335, 596, 685, 813, 1226, 1336, 1450, 1527, 1827, 1956, 2036, 2068, 2163];
        $allStories = [];
        foreach ($storyIds as $storyId) {
            $storyModel = Story::findOne($storyId);
            if ($storyModel === null) {
                throw new NotFoundHttpException("История не найдена");
            }
            $storyData = $this->editorService->jsonFromStory($storyModel->slidesData(), Yii::$app->urlManagerFrontend->createAbsoluteUrl(['preview/view', 'alias' => $storyModel->alias]), $storyModel->title);
            $allStories[] = $storyData["content"];
        }
        //$response->sendContentAsFile(json_encode($allStories, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'slides.json');
        $response->sendContentAsFile(implode("\n\n", $allStories), 'slides.txt');
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionGetText(int $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $story = $this->findModel(Story::class, $id);
        return [
            'text' => $this->editorService->textFromStory(
                SlideModifier::slidesToHTML($story->getSlidesWithLinkData()),
            ),
        ];
    }

    public function actionSetCover(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $storyId = (int) $payload['storyId'];
        $imageUrl = $payload['imageUrl'];

        try {

            $story = $this->findModel(Story::class, $storyId);
            /*$savePath = Yii::getAlias('@public/upload/gen-images/' . $story->id);
            FileHelper::createDirectory($savePath);
            $imageFilePath = $this->imageService->downloadImage($imageUrl, $savePath);
            if (!file_exists($imageFilePath)) {
                throw new \DomainException('Download image error');
            }*/

            $imageFilePath = str_replace('/admin', '', Yii::getAlias('@webroot')) . $imageUrl;
            if (realpath($imageFilePath) !== $imageFilePath) {
                throw new NotFoundHttpException('Изображение не найдено');
            }

            $coverFilePath = StoryCover::getCoverFolderPath(true) . DIRECTORY_SEPARATOR . basename($imageFilePath);
            if (!copy($imageFilePath, $coverFilePath)) {
                throw new DomainException('Copy image error');
            }

            StoryCover::create($imageFilePath);

            $story->cover = basename($imageFilePath);
            if (!$story->save(false)) {
                throw new DomainException('Save cover error');
            }

            return ['success' => true];

        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function actionSaveImage(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $storyId = (int) $payload['storyId'];
        $imageUrl = $payload['imageUrl'];

        try {
            $story = $this->findModel(Story::class, $storyId);
            $savePath = Yii::getAlias('@public/upload/gen-images/' . $story->id);
            FileHelper::createDirectory($savePath);
            $imageFilePath = $this->imageService->downloadImage($imageUrl, $savePath);
            if (!file_exists($imageFilePath)) {
                throw new DomainException('Download image error');
            }
            return [
                'success' => true,
                'key' => basename($imageFilePath),
                'url' => '/upload/gen-images/' . $story->id . '/' . basename($imageFilePath),
            ];
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionGenImages(int $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $story = $this->findModel(Story::class, $id);
        $imagesPath = Yii::getAlias('@public/upload/gen-images/' . $story->id);
        if (!is_dir($imagesPath)) {
            return ['success' => true, 'images' => []];
        }
        return [
            'success' => true,
            'images' => array_map(static function (string $fileName) use ($story): array {
                return [
                    'key' => basename($fileName),
                    'url' => '/upload/gen-images/' . $story->id . '/' . basename($fileName),
                ];
            }, FileHelper::findFiles($imagesPath)),
        ];
    }
}
