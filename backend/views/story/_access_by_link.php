<?php
use backend\models\StoryAccessByLinkForm;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $story common\models\Story */
$accessByLinkForm = new StoryAccessByLinkForm($story);
?>
<div class="modal fade" id="access-by-link-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Доступ по ссылке</h4>
            </div>
            <div class="modal-body">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($accessByLinkForm, 'access_link')->textInput(['readonly' => true]) ?>
            <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary hide" data-loading-text="Загрузка..." id="grant-access">Предоставить доступ</button>
                <button class="btn btn-default hide" data-loading-text="Загрузка..." id="revoke-access">Отменить доступ</button>
            </div>
        </div>
    </div>
</div>
<?php
$storyID = $accessByLinkForm->getStoryID();
$linkAccessAllowed = var_export($accessByLinkForm->linkAccessAllowed(), true);
$js = <<< JS
var linkAccessAllowed = $linkAccessAllowed,
    storyID = $storyID;
var modal = $('#access-by-link-modal'),
    revokeAccess = $('#revoke-access', modal),
    grantAccess = $('#grant-access', modal)
    linkInput = $('#storyaccessbylinkform-access_link', modal);
function showButtons() {
    $('.modal-footer button', modal).addClass('hide');
    if (linkAccessAllowed) {
        revokeAccess.removeClass('hide');
    }
    else {
        grantAccess.removeClass('hide');
    }
}
modal.on('shown.bs.modal', function() {
    showButtons();
});
grantAccess.on('click', function() {
    grantAccess.button('loading');
    $.getJSON('/admin/index.php?r=story/grant-access-by-link', {'id': storyID})
        .done(function(response) {
            if (response && response.success) {
                linkInput.val(response.accessLink);
                linkAccessAllowed = true;
                showButtons();
            }
        })
        .fail(function(response) {
            toastr.error(response.responseText);
        })
        .always(function() {
            grantAccess.button('reset');
        });
});
revokeAccess.on('click', function() {
    revokeAccess.button('loading');
    $.getJSON('/admin/index.php?r=story/revoke-access-by-link', {'id': storyID})
        .done(function(response) {
            if (response && response.success) {
                linkInput.val('');
                linkAccessAllowed = false;
                showButtons();
            }
        })
        .fail(function(response) {
            toastr.error(response.responseText);
        })
        .always(function() {
            revokeAccess.button('reset');
        });
});
JS;
$this->registerJs($js);