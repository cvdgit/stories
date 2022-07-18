<?php

namespace modules\files\services;

use modules\files\forms\UpdateStudyFileForm;
use common\components\ModelDomainException;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\files\forms\CreateStudyFileForm;
use modules\files\forms\FilesUploadForm;
use modules\files\models\StudyFile;
use modules\files\models\StudyFileHistory;
use modules\files\models\StudyFileStatus;
use modules\files\models\StudyFolder;
use Ramsey\Uuid\Uuid;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class StudyFileService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function uploadFile(StudyFolder $folderModel, UploadedFile $file, string $newFileName): void
    {
        $folder = $folderModel->getFolderPath();
        FileHelper::createDirectory($folder);

        $filePath = $folder . '/' . $newFileName . '.' . $file->extension;
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }

        if (!$file->saveAs($filePath)) {
            throw new DomainException('File save error');
        }
    }

    public function createFileModel(string $uuid, string $name, int $folderId, string $type, string $alias = null, int $status = StudyFileStatus::STATUS_ACTIVE): StudyFile
    {
        $fileModel = StudyFile::create($uuid, $name, $folderId, $type, $alias, $status);
        if (!$fileModel->save()) {
            throw ModelDomainException::create($fileModel);
        }
        return $fileModel;
    }

    public function create(CreateStudyFileForm $form): int
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $folderModel = StudyFolder::findOne($form->folder_id);
        $uuid = Uuid::uuid4();
        /** @var UploadedFile $file */
        $file = $form->file;
        $this->uploadFile($folderModel, $file, $uuid);

        $fileModel = $this->createFileModel($uuid, $file->baseName, $form->folder_id, $file->extension, $form->alias);
        return $fileModel->id;
    }

    public function update(StudyFile $fileModel, UpdateStudyFileForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        if ($form->file !== null) {
            $folderModel = $fileModel->folder;
            $this->uploadFile($folderModel, $form->file, $fileModel->uuid);
        }

        $fileModel->updateFile($form->name, $form->folder_id, $form->status, $form->alias);
        if (!$fileModel->save()) {
            throw ModelDomainException::create($fileModel);
        }
    }

    public function uploadFiles(StudyFolder $folderModel, FilesUploadForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $this->transactionManager->wrap(function() use ($form, $folderModel) {

            foreach ($form->files as $file) {

                $uuid = Uuid::uuid4();
                $this->uploadFile($folderModel, $file, $uuid);

                $this->createFileModel($uuid, $file->baseName, $folderModel->id, $file->extension);
            }
        });
    }

    public function addOpenHistory(int $userId, int $studyFileId): void
    {
        $model = StudyFileHistory::create($userId, $studyFileId);
        if (!$model->save()) {
            throw ModelDomainException::create($model);
        }
    }
}
