<?php
use common\helpers\Url;
use yii\helpers\Json;
/** @var $testModel common\models\StoryTest */
$css = <<<CSS
.tests-manage {
    min-height: 500px;
    font-size: 14px;
}
.tests-manage-test-list {
    max-height: 450px;
    min-height: 400px;
    overflow: hidden;
    overflow-y: auto;
    margin-bottom: 0;
}
.tests-manage-test-list li {
    padding: 6px 10px;
    height: 34px;
    overflow: hidden;
}
.tests-manage-test-list li .badge {
    background-color: transparent !important;
    color: #000;
}
.tests-manage-test-list [data-test-id] {
    cursor: pointer;
}
.tests-manage-test-list li span.text-wrapper {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    max-width: 90%;
    margin-right: -32px;
    float: left;
}
CSS;
$this->registerCss($css);
?>
<button class="btn btn-primary" type="button" id="manage-tests">Выбрать тесты</button>
<div style="margin-top: 20px">
    <ul class="list-group tests-manage-test-list" id="tests-lists-preview" style="height: auto; max-height: none; min-height: auto; overflow: hidden">
        <li class="list-group-item">
            <span class="text-wrapper" title="Список тестов пуст">Список тестов пуст</span>
        </li>
    </ul>
</div>

<div class="modal remote fade" id="manage-tests-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$remote = Url::to(['tests/manage', 'test_id' => $testModel->id]);
$testID = $testModel->id;
$relatedTests = Json::encode($testModel->relatedTests);
$js = <<<JS
(function() {
    
    var modal = $('#manage-tests-modal');
    var tests = $relatedTests;

    function createTestsList() {
        var list = $('#tests-lists-preview');
        if (tests.length > 0) {
            list.empty();
        }
        tests.forEach(function(item) {
            $('<li/>', {'class': 'list-group-item'}).append(
                $('<span/>', {
                    'class': 'text-wrapper',
                    'text': item.title,
                    'title': item.title
                })
            ).appendTo(list);
        });
    }
    createTestsList();
    
    $('#manage-tests').on('click', function() {
        modal.modal({'remote': '$remote'});
    });
    
    modal.on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
    
    modal.on('loaded.bs.modal', function() {
        
        var allTestsList = modal.find('#all-tests-list');
        var selectedTestsList = modal.find('#selected-tests-list');
        
        allTestsList.on('click', '[data-test-id]', function() {
            $(this)
                .parent().find('i')
                .removeClass('glyphicon-plus')
                .addClass('glyphicon-minus')
                .end()
                .appendTo(selectedTestsList);
        });
        
        selectedTestsList.on('click', '[data-test-id]', function() {
            $(this)
                .parent().find('i')
                .removeClass('glyphicon-minus')
                .addClass('glyphicon-plus')
                .end()
                .appendTo(allTestsList);
        });
        
        modal.find('#save-selected-tests').on('click', function() {
            var formData = new FormData();
            formData.append('RelatedTestsForm[test_id]', $testID);
            selectedTestsList.find('[data-test-id]').each(function() {
                formData.append('RelatedTestsForm[test_ids][]', $(this).attr('data-test-id'));
            });
            var button = $(this);
            button.button("loading");
            $.ajax({
                url: '/admin/index.php?r=tests/create',
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(response) {
                if (response && response.success) {
                    toastr.success('Изменения успешно сохранены');
                    tests = response.tests;
                }
            })
            .fail(function(response) {
                toastr.error(response.responseJSON.type, response.responseJSON.message);
            })
            .always(function() {
                button.button('reset');
                createTestsList();
                modal.modal('hide');
            });
        });
        
        var timeout;
        function filterTests(val) {
            allTestsList.find('li').show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        }
        modal.find('#tests-filter').on('input', function() {
            if (timeout) {
                clearTimeout(timeout);
            }
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            timeout = setTimeout(function() {
                filterTests(val);
            }, 300);
        })
    });
})();
JS;
$this->registerJs($js);
