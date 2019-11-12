<?php

use common\models\Playlist;
use yii\bootstrap\Modal;
use yii\widgets\Menu;

/** @var $this yii\web\View */
/** @var $selectInputID string */

Modal::begin([
    'id' => 'select-playlists-modal',
    'header' => '<h2>Плейлисты</h2>',
    'toggleButton' => ['label' => 'Выбрать плейлист', 'class' => 'btn btn-default'],
]);
?>
<div class="form-inline" style="margin-bottom: 20px">
    <div class="form-group">
        <input type="text" id="playlist-title" class="form-control" value="" maxlength="255" aria-required="true" />
    </div>
    <button type="button" class="btn btn-default" id="create-playlist">Создать</button>
</div>
<div id="playlists-list">
    <?= Menu::widget([
        'items' => Playlist::playlistsArray(),
        'encodeLabels' => false,
        'linkTemplate' => '<label><input type="checkbox" value="{url}"> {label}</label>',
    ]) ?>
</div>
<div class="clearfix">
    <div class="pull-right">
        <button type="button" class="btn btn-success" id="save-playlists">Сохранить</button>
    </div>
</div>
<?php Modal::end() ?>

<?php
$js = <<< JS
$('#select-playlists-modal')
    .on("shown.bs.modal", function() {
        $("#playlist-title").focus();
    })
    .on('show.bs.modal', function() {
        $("#playlist-title").val("");
        var list = $('#playlists-list'),
            id = '$selectInputID';
        $('input[type=checkbox]', list).prop('checked', false);
        var value = $('#' + id).val();
        if (value) {
            value.split(',').forEach(function(value) {
                $('input[value=' + value + ']', list).prop('checked', true);
            });
        }
    });
$('#save-playlists').on('click', function() {
    var list = $('#selected-playlists-list'),
        id = '$selectInputID',
        ids = [];
    list.empty();
    $('#playlists-list input[type=checkbox]').each(function() {
        var el = $(this);
        if (el.is(':checked')) {
            $('<span>')
              .addClass('label label-default')
              .text($.trim(el.parent().text()))
              .appendTo(list);
            list.append(' ');
            ids.push(el.val());
        }
    });
    $('#' + id).val(ids.join(',')).blur();
    $('#select-playlists-modal').modal('hide');
});
$("#create-playlist").on("click", function() {
    var title = $("#playlist-title").val();
    if (!title) {
        return;
    }
    var list = $("#playlists-list");
    $.get("index.php?r=playlist/create&title=" + title)
        .then(function(data) {
            if (data && data.success) {
                if (!list.find("ul").length) {
                    $("<ul/>").appendTo(list);
                }
                $('<li><label><input type="checkbox" value="' + data.playlist.id + '"> ' + data.playlist.title + '</label></li>')
                    .appendTo($("ul", list));
            }
        });
});
JS;
$this->registerJs($js);
?>