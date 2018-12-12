<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;

$session = Yii::$app->session;
$passwordMessage = $session->get('password-message');
$session->remove('password-message');
?>
    <div class="container bootstrap snippet">
    <div class="row">
        <div class="col-sm-10"><h4 class="cst-padding-20"><?= $model->username ?></h4></div>
    </div>
    <div class="row">
        <div class="col-sm-3"><!--left col-->

      <div class="text-center">
        <?php $background = (isset($model->image)) ? '/uploads/' . $model->image : 'http://ssl.gstatic.com/accounts/ui/avatar_2x.png';  ?>
        <div class="cst-box-image">
            <div class="cst-image-div" id="image-preview" style="background-image: url(<?= $background  ?>)"></div>
        </div>
        <p>Загрузить другую фотографию...</p>
        <div class="file-upload">
            <form id="form-upload">
                <label>
                    <input type="file" id="image-upload" name="image-upload" accept="image/*,image/jpeg">
                    <span id="fileButton">Выбрать файл</span>
                </label>
            </form>
        </div>
        <input type="text" id="filename" class="filename" disabled>

      </div>           
          
        </div><!--/col-3-->
        <div class="col-sm-9">
            <ul class="nav nav-tabs cst-nav">
                <li class="<?= !isset($passwordMessage) ? 'active' : '' ?>"><a data-toggle="tab" href="#home">Основная инфомация</a></li>
                <li><a data-toggle="tab" href="#messages">Моя подписка</a></li>
                <li class="<?= isset($passwordMessage) ? 'active' : '' ?>"><a data-toggle="tab" href="#settings">Сменить пароль</a></li>
              </ul>

              
          <div class="tab-content">
            <div class="tab-pane <?= !isset($passwordMessage) ? 'active' : '' ?>" id="home">
                <hr class="cst-none">
                <div class="row widget-search customers cst-margin-left-20">
                    <div class="col-xs-6">

                        <div class="el-container">
                            <label class="widget-title" for="user-username" style="margin-bottom: 5px;">Имя пользователя:</label>
                            <?= Html::tag('p', Html::encode($model->username), ['class' => 'title']) ?>
                        </div>

                        <div class="el-container">
                            <label class="widget-title" for="user-email" style="margin-bottom: 5px;">Email:</label>
                            <?= Html::tag('p', Html::encode($model->email), ['class' => 'title']) ?>
                        </div>
                        
                    </div>
                </div>
              
             </div><!--/tab-pane-->
             <div class="tab-pane" id="messages">
                <hr class="cst-none">   

                <div class="cst-margin-left-20">
                    <div class="info-title">
                        <?php if (isset($count_date_rate)): ?>
                            <p>Количесво дней : <?= $count_date_rate ?></p>
                        <?php else: ?>
                            <p>Преобретите <?= Html::a('подписку', ['/pricing']) ?> 
                            для просмотра всех историй</p>
                        <?php endif ?>
                    </div>
                    <?php if (isset($count_date_rate)): ?>
                        <br>
                        <div class="row customers">
                            <div class="col-md-4">
                                <div class="inside cst-sub">
                                    <p class="cst-padding-none"><?= $rate->title ?></p>
                                    <p class="cst-cost">Премиум</p>
                                    <input type=submit value="Продлить" class="cst-btn text-center white">
                                    <div class="inside-inside">
                                        <span><?= $rate->description ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>

             </div><!--/tab-pane-->
             <div class="tab-pane <?= isset($passwordMessage) ? 'active' : '' ?>" id="settings">
                <div class="cst-margin-left-20">
                    <hr class="cst-none">   
                    <?php if (isset($passwordMessage)): ?>
                        <div class="row">
                            <div class="alert alert-primary cst-alert col-xs-6" role="alert">
                                <?= $passwordMessage ?>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="widget-search">
                        <?php $form = ActiveForm::begin(['action' => ['change-password'],
                            'fieldConfig' => [
                                'template' => '<div class="el-container">{label}{input}{error}</div>',
                                'labelOptions' => ['class' => 'widget-title'],
                                'inputOptions' => ['class'=> null]
                            ]
                        ]); ?>
                            <div class="col-xs-6">
                                <?= $form->field($modelPassword, 'password', ['inputOptions' => ['placeholder' => 'Пароль']])->passwordInput(['autofocus' => true]) ?>
                                <?= $form->field($modelPassword, 'passwordRepeat', ['inputOptions' => ['placeholder' => 'Повторить пароль']])->passwordInput() ?>
                            </div>
                            <div class="form-group col-xs-12">
                                <?= Html::submitButton('Изменить', ['class' => 'custom-btn form-btn cst-btn-mini', 'name' => 'save-button']) ?>  
                            </div>
                        <?php ActiveForm::end(); ?>
                    </div>

                </div>
                </div>
              </div><!--/tab-pane-->
          </div><!--/tab-content-->

        </div><!--/col-9-->
    </div><!--/row-->
<!-- </div> -->