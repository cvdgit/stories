<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\User;
use common\rbac\UserRoles;
use frontend\components\UserController;
use frontend\Game\Deploy\ArchUploadCommand;
use frontend\Game\Deploy\ArchUploadHandler;
use frontend\Game\Deploy\DeployCommand;
use frontend\Game\Deploy\DeployForm;
use frontend\Game\Deploy\DeployHandler;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\User as WebUser;

class GameController extends UserController
{
    public $layout = "game";

    /**
     * @var DeployHandler
     */
    private $deployHandler;

    /**
     * @var ArchUploadHandler
     */
    private $archUploadHandler;

    public function __construct($id, $module, ArchUploadHandler $archUploadHandler, DeployHandler $deployHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->deployHandler = $deployHandler;
        $this->archUploadHandler = $archUploadHandler;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionShow(WebUser $user): string
    {
        $defaultConfig = [
            "id" => $user->getId(),
            "health" => 300,
            "isAlive" => true,
            "sceneToLoad" => 3,
            "testSuccess" => false,
            "stories" => [],
        ];

        $data = (new Query())
            ->select("data")
            ->from("game_data")
            ->where([
                'user_id' => $user->getId()
            ])
            ->scalar();

        $config = $defaultConfig;
        if ($data) {
            $config = Json::decode($data);
        }

        $deploy = (new Query())
            ->select(['name', 'folder'])
            ->from('game_deploy')
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if ($deploy === false) {
            throw new NotFoundHttpException('Сборка не найдена');
        }

        $folder = '/game/' . $deploy['folder'] . '/' . $deploy['name'];

        return $this->render("show", [
            "config" => Json::encode($config),
            "loaderConfig" => Json::encode([
                'folder' => $folder,
            ]),
            'folder' => $folder,
        ]);
    }

    /**
     * @throws Exception
     * @return Response|string
     */
    public function actionDeploy(Request $request, WebUser $user)
    {
        $userModel = User::findOne($user->getId());
        if ($userModel === null) {
            throw new ForbiddenHttpException('Access denied');
        }

        $deployEmail = Yii::$app->params['game.deploy.email'] ?? '';

        if (!Yii::$app->user->can(UserRoles::ROLE_ADMIN) && !($deployEmail && $deployEmail === $userModel->email)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $form = new DeployForm();
        $form->buildName = 'BildForDemo11';
        if ($form->load($request->post())) {
            $form->zipFile = UploadedFile::getInstance($form, 'zipFile');
            if ($form->validate()) {

                $fileName = Uuid::uuid4()->toString();

                $this->archUploadHandler->handle(new ArchUploadCommand(
                    $archFolder = Yii::getAlias('@public/game/arch'),
                    $archFileName = $fileName . '.' . $form->zipFile->extension,
                    $form->zipFile
                ));

                $folder = Yii::getAlias('@public/game/'. $fileName);
                $this->deployHandler->handle(new DeployCommand(
                    $archFolder . '/' . $archFileName,
                    $folder,
                    $form->buildName
                ));

                $command = Yii::$app->db->createCommand();
                $command->insert('game_deploy', [
                    'name' => $form->buildName,
                    'folder' => $fileName,
                    'created_at' => time(),
                ]);
                $command->execute();

                return $this->redirect(['game/show']);
            }
        }
        return $this->render('deploy', [
            'formModel' => $form,
        ]);
    }
}
