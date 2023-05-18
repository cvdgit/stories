<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Import;

use backend\Testing\ImportQuestions\Form\QuestionsImportForm;
use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class ImportAction extends Action
{
    private $importHandler;

    public function __construct($id, $controller, ImportHandler $importHandler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->importHandler = $importHandler;
    }

    public function run(Request $request, Response $response): array
    {
        $importForm = new QuestionsImportForm();
        if ($importForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$importForm->validate()) {
                return ['success' => false, 'message' => 'not valid'];
            }

            try {
                $this->importHandler->handle(new ImportCommand((int) $importForm->from_test_id, (int) $importForm->to_test_id, $importForm->questions));
                return ['success' => true, 'message' => 'Вопросы успешно импортированы', 'data' => $importForm->questions];
            } catch (\Exception $exception) {
                return ['success' => false, 'message' => 'Импорт не удался по причине: ' . $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
