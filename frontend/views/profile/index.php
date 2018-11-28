<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="container bootstrap snippet">
    <div class="row">
  		<div class="col-sm-10"><h2 class="cst-padding-20"><?= $model->username ?></h2></div>
    </div>
    <div class="row">
  		<div class="col-sm-3"><!--left col-->
              

      <div class="text-center">
        <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
        <h6>Загрузить другую фотографию...</h6>
        <input type="file" class="text-center center-block file-upload cst-upload">
      </div>           
          
        </div><!--/col-3-->
    	<div class="col-sm-9">
            <ul class="nav nav-tabs cst-nav">
                <li class="active"><a data-toggle="tab" href="#home">Основная инфомация</a></li>
                <li><a data-toggle="tab" href="#messages">Моя подписка</a></li>
                <li><a data-toggle="tab" href="#settings">Сменить пароля</a></li>
              </ul>

              
          <div class="tab-content">
            <div class="tab-pane active" id="home">
                <hr class="cst-none">
                <div class="widget-search">
                    <?php $form = ActiveForm::begin(['id' => 'login-form',
                        'fieldConfig' => [
                            'template' => '<div class="el-container">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'widget-title'],
                            'inputOptions' => ['class'=> null]
                        ]
                    ]); ?>
                        <div class="col-xs-6">
                            <?= $form->field($model, 'username', ['inputOptions' => ['placeholder' => 'Имя пользователя']])->textInput(['autofocus' => true]) ?>
                            <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => 'E-mail пользоваеля']]) ?>
                            <?= $form->field($model, 'group', ['inputOptions' => ['placeholder' => 'Группа']]) ?>
                        </div>
                        <div class="form-group col-xs-12">
                            <?= Html::submitButton('Сохранить', ['class' => 'custom-btn form-btn cst-mrg-lft-rgh-15', 'name' => 'save-button']) ?>  
                            <?= Html::submitButton('Отменить', ['class' => 'custom-btn white form-btn cst-mrg-lft-rgh-15', 'name' => 'cancel-button']) ?>  
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
              
             </div><!--/tab-pane-->
             <div class="tab-pane" id="messages">
                <hr class="cst-none">                 
                  
                <div class="row customers">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="inside cst-sub">
                            <p class="cst-padding-none">3 месяца</p>
                            <p class="cst-cost">Премиум</p>
                            <input type=submit value="Продлить" class="cst-btn text-center white">
                            <div class="inside-inside">
                                <span>доступ ко всем историям на 3 месяца</span>
                            </div>
                        </div>
                    </div>
                </div>

             </div><!--/tab-pane-->
             <div class="tab-pane" id="settings">
                <hr class="cst-none">	
                <div class="widget-search">
                    <?php $form = ActiveForm::begin(['id' => 'login-form',
                        'fieldConfig' => [
                            'template' => '<div class="el-container">{label}{input}{error}</div>',
                            'labelOptions' => ['class' => 'widget-title'],
                            'inputOptions' => ['class'=> null]
                        ]
                    ]); ?>
                        <div class="col-xs-6">
                            <div class="el-container">
                                <label class="widget-title" for="user-password">Пароль</label>
                                <input type="password" id="user-password" name="password" placeholder="Пароль" autofocus="true">
                                <p class="help-block help-block-error"></p>
                            </div>
                            <div class="el-container">
                                <label class="widget-title" for="user-password-repeat">Повторить пароль</label>
                                <input type="password" id="user-password-repeat" name="password-repeat" placeholder="Повторить пароль">
                                <p class="help-block help-block-error"></p>
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <?= Html::submitButton('Изменить', ['class' => 'custom-btn form-btn cst-mrg-lft-rgh-15', 'name' => 'save-button']) ?>  
                            <?= Html::submitButton('Отменить', ['class' => 'custom-btn white form-btn cst-mrg-lft-rgh-15', 'name' => 'cancel-button']) ?>  
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>

              </div>
               
              </div><!--/tab-pane-->
          </div><!--/tab-content-->

        </div><!--/col-9-->
    </div><!--/row-->
<!-- </div> -->

