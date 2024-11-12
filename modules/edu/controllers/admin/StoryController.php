<?php

declare(strict_types=1);

namespace modules\edu\controllers\admin;

use Exception;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduLesson;
use modules\edu\models\EduStory;
use modules\edu\models\EduTopic;
use modules\edu\services\LessonService;
use modules\edu\Story\AddStoryForm;
use Yii;
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

class StoryController extends Controller
{
    /**
     * @var LessonService
     */
    private $lessonService;

    public function __construct($id, $module, LessonService $lessonService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->lessonService = $lessonService;
    }

    public function actionIndex(): string
    {
        $pathQuery = (new Query())
            ->from(['sl' => 'edu_lesson_story'])
            ->innerJoin(['l' => 'edu_lesson'], 'sl.lesson_id = l.id')
            ->innerJoin(['t' => 'edu_topic'], 'l.topic_id = t.id')
            ->innerJoin(['cp' => 'edu_class_program'], 't.class_program_id = cp.id')
            ->innerJoin(['p' => 'edu_program'], 'cp.program_id = p.id')
            ->innerJoin(['c' => 'edu_class'], 'cp.class_id = c.id')
            ->where('s.id = sl.story_id');

        $fromDate = (new \DateTime())->modify('-2year')->format('Y-m-d');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$fromDate 00:00:00')");

        $toDate = (new \DateTime())->format('Y-m-d');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$toDate 23:59:59')");

        $query = (new Query())
            ->select([
                'id' => 's.id',
                'title' => 's.title',
                'alias' => 's.alias',
                'publishedAt' => 's.published_at',
                'path' => $pathQuery->select(
                    new Expression(
                        "GROUP_CONCAT(CONCAT(
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/class/update&id=', c.id, '\">', c.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/program/update&id=', p.id, '\">', p.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/topic/update&id=', t.id, '\">', t.name, '</a>'),
                        ' / ',
                        CONCAT('<a target=\"_blank\" href=\"/admin/index.php?r=edu/admin/lesson/update&id=', l.id, '\">', l.name, '</a>')
                        ) SEPARATOR ', ')",
                    ),
                ),
                'author' => new Expression("COALESCE(CONCAT(p.last_name, ' ', p.first_name), u.username)"),
            ])
            ->from(['s' => 'story'])
            ->innerJoin(['u' => 'user'], 's.user_id = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
            ->where('s.published_at IS NOT NULL')
            ->andWhere(['between', 's.published_at', $betweenBegin, $betweenEnd]);

        $dataProvider = new SqlDataProvider([
            'sql' => $query->createCommand()->getRawSql(),
            'totalCount' => $query->count(),
            'sort' => [
                'attributes' => [
                    'id',
                    'title',
                    'publishedAt',
                    'author',
                ],
                'defaultOrder' => ['publishedAt' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        $addStoryForm = new AddStoryForm();

        $json = Json::encode([
            'classItems' => EduClass::find()->orderBy(['name' => SORT_ASC])->all(),
            'classProgramItems' => array_map(static function(EduClassProgram $item): array {
                return ['id' => $item->id, 'class_id' => $item->class_id, 'name' => $item->program->name];
            }, EduClassProgram::find()->all()),
            'topicItems' => array_map(static function(EduTopic $item): array {
                return ['id' => $item->id, 'class_program_id' => $item->class_program_id, 'name' => $item->name];
            }, EduTopic::find()->orderBy(['order' => SORT_ASC])->all()),
            'lessonItems' => array_map(static function(EduLesson $item): array {
                return ['id' => $item->id, 'topic_id' => $item->topic_id, 'name' => $item->name];
            }, EduLesson::find()->orderBy(['order' => SORT_ASC])->all()),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'formModel' => $addStoryForm,
            'json'=>  $json,
        ]);
    }

    public function actionAddStory(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $addStoryForm = new AddStoryForm();
        if ($addStoryForm->load($request->post())) {
            if (!$addStoryForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $story = EduStory::findOne($addStoryForm->story_id);
            if ($story === null) {
                return ['success' => false, 'message' => 'Story not found'];
            }

            $class = EduClass::findOne($addStoryForm->class_id);
            if ($class === null) {
                return ['success' => false, 'message' => 'Class not found'];
            }

            $classProgram = EduClassProgram::findOne($addStoryForm->class_program_id);
            if ($classProgram === null) {
                return ['success' => false, 'message' => 'Class Program not found'];
            }

            $topic = EduTopic::findOne($addStoryForm->topic_id);
            if ($topic === null) {
                return ['success' => false, 'message' => 'Topic not found'];
            }

            $lesson = EduLesson::findOne($addStoryForm->lesson_id);
            if ($lesson === null) {
                return ['success' => false, 'message' => 'Lesson not found'];
            }

            $links = [
                '<a target="_blank" href="/admin/index.php?r=edu/admin/class/update&id=' . $class->id . '">' . $class->name . '</a>',
                '<a target="_blank" href="/admin/index.php?r=edu/admin/program/update&id=' . $classProgram->program_id . '">' . $classProgram->program->name . '</a>',
                '<a target="_blank" href="/admin/index.php?r=edu/admin/topic/update&id=' . $topic->id . '">' . $topic->name . '</a>',
                '<a target="_blank" href="/admin/index.php?r=edu/admin/lesson/update&id=' . $lesson->id . '">' . $lesson->name . '</a',
            ];

            try {
                $this->lessonService->addStoryRaw($lesson->id, $story->id);
                return ['success' => true, 'storyId' => $addStoryForm->story_id, 'links' => implode(' / ', $links)];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
