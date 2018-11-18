<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <table class="table">
        <tbody>
            <tr>
                <td>Имя пользователя</td>
                <td><?= $model->username ?></td>
            </tr>
            <tr>
                <td>E-mail пользоваеля</td>
                <td><?= $model->email ?></td>
            </tr>
            <tr>
                <td>Статус</td>
                <td><?= $model->status ?></td>
            </tr>
            <tr>
                <td>Группа</td>
                <td><?= $model->group ?></td>
            </tr>
            <tr>
                <td>Дата создания аккаунта</td>
                <td><?= $model->created_at ?></td>
            </tr>
        </tbody>
    </table>
</div>

