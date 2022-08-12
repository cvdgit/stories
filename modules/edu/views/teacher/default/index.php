<?php

declare(strict_types=1);

use modules\edu\models\EduClassBook;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var EduClassBook[] $classBooks
 */

$this->title = 'Учительская доска';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top:0">

            <h1 class="h2" style="margin-bottom:20px"><?= Html::encode($this->title) ?></h1>

            <table class="table table-hover">
                <tbody>
                    <?php foreach ($classBooks as $classBook): ?>
                    <?php foreach ($classBook->eduClassBookPrograms as $classProgram): ?>
                    <tr>
                        <td><?= $classBook->name ?></td>
                        <td><?= $classProgram->classProgram->program->name ?></td>
                        <td>
                            <?= Html::a('<i class="glyphicon glyphicon-chevron-right" style="font-size: 24px"></i>', [
                                '/edu/teacher/default/class-program-stats',
                                'class_book_id' => $classBook->id,
                                'class_program_id' => $classProgram->classProgram->id
                            ]) ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
