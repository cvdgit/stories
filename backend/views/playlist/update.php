<?php
use common\models\story\StoryStatus;
use yii\helpers\Html;
use yii\jui\JuiAsset;
/** @var $model common\models\Playlist */
$this->title = 'Плейлист: ' . $model->title;
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered table-hover">
                <tbody id="story-list">
                <?php foreach ($model->storiesAdmin as $story): ?>
                    <tr data-story-id="<?= $story['id'] ?>">
                        <td><?= $story['playlist_order'] ?></td>
                        <td><?= $story['title'] ?></td>
                        <td><?= StoryStatus::asText($story['status']) ?></td>
                        <td>
                            <?= Html::a('Удалить', ['playlist/delete-item', 'playlist_id' => $model->id, 'story_id' => $story['id']], ['class' => 'delete-playlist-item']) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$action = \common\helpers\Url::to(['playlist/order', 'playlist_id' => $model->id]);
$js = <<< JS
$("#story-list").sortable({
    placeholder: "ui-state-highlight",
    stop: function(event, ui) {
        var stories = [];
        $("#story-list tr").each(function(i, row) {
            stories.push($(row).attr("data-story-id"));
        });
        $.post("$action", {"stories": stories}).then(function(data) {
            console.log(data);
        })
    }
});
$("#story-list").disableSelection();
$("#story-list").on("click", "a.delete-playlist-item", function(e) {
    e.preventDefault();
    if (!confirm("Удалить запись?")) {
        return;
    }
    var link = $(this);
    $.get(link.attr("href")).then(function(data) {
        if (data && data.success) {
            link.parent().parent().remove();
        }
    });
});
JS;
/** @var $this yii\web\View */
$this->registerJs($js);
JuiAsset::register($this);