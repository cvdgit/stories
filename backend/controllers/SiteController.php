<?php
namespace backend\controllers;

use common\services\auth\AuthService;
use DomainException;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\StoryStatisticsSearch;
use common\rbac\UserRoles;
use yii\web\Controller;
use yii\web\ErrorAction;

/**
 * Site controller
 */
class SiteController extends Controller
{

    protected $service;

    public function __construct($id, $module, AuthService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_ADMIN_PANEL],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $statisticsModel = new StoryStatisticsSearch();
        $dataProvider = $statisticsModel->getChartData4();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {
        $this->layout = 'login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $user = $this->service->auth($model);
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                return $this->goBack();
            } catch (DomainException $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', 'Неверное имя пользователя или пароль.');
                return $this->refresh();
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
