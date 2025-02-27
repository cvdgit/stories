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
use modules\edu\Story\EduStorySearch;
use Yii;
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

    public function actionIndex(Request $request): string
    {
        $searchModel = new EduStorySearch();
        $dataProvider = $searchModel->search($request->get());

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
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'formModel' => new AddStoryForm(),
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

    public function actionProgress(int $id): string
    {
        $query = (new Query())
            ->select([
                't.student_id',
                'studentName' => 'us.name',
                'sessionId' => 't.session',
                'time' => 'MAX(t.created_at)',
                't.slide_id',
                'slideNumber' => 's.number',
                'slideType' => 's.kind',
                'slideStatus' => 's.status',
            ])
            ->from(['t' => 'story_student_stat'])
            ->leftJoin(['us' => 'user_student'], 't.student_id = us.id')
            ->leftJoin(['s' => 'story_slide'], 't.slide_id = s.id')
            ->where(['t.story_id' => $id])
            ->groupBy(['t.student_id', 't.session', 't.slide_id'])
            ->orderBy(['MAX(t.created_at)' => SORT_ASC]);
        $rows = $query->all();



        return $this->render('progress', [
            'rows' => $rows,
        ]);
    }
}
