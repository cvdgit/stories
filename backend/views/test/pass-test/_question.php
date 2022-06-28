<?php
use backend\models\pass_test\CreatePassTestForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
$this->registerCss(<<<CSS
.highlight {
    /*color: red;
    cursor:pointer;*/
    user-select: none;
}
.content:focus-visible {
    outline: none;
}
.content {
    border: 1px #d0d0d0 solid;
    padding: 10px;
    min-height: 300px;
}
.content__title label {
    margin-bottom: 0;
}
.fragment-item {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 3px 0;
}
.fragment-item:hover {
    color: #262626;
    text-decoration: none;
    background-color: #f5f5f5;
}
.fragment-input {
    display: block;
    padding: 0 10px;
}
.fragment-input input {
    cursor: pointer;
}
.fragment-title {
    display: block;
    margin-right: auto;
    padding: 0 10px;
    flex: auto;
}
.fragment-title > a {
    display: block;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333333;
    white-space: nowrap;
    text-decoration: none;
}
.fragment-title > a:focus-visible {
    outline: none;
}
.fragment-title > a:hover {
    text-decoration: none;
}
.fragment-action {
    display: block;
    padding: 0 10px;
}
.fragment-action a {
    display: block;
    width: 16px;
    height: 16px;
}
CSS
);
/** @var CreatePassTestForm $model */
/** @var bool $isNewRecord */
?>
<?php $form = ActiveForm::begin(['id' => 'pass-test-form']) ?>
<?= $form->field($model, 'name') ?>
<div>
    <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
        <div class="content__title">
            <?= Html::activeLabel($model, 'content') ?>
        </div>
        <div style="margin-left:auto">
            <button class="btn btn-primary btn-sm" id="add" type="button">Вставить пропуск</button>
        </div>
    </div>
    <div style="min-height:300px;max-height:300px;overflow-y:auto">
        <div class="content" id="content" contenteditable="true"></div>
    </div>
    <?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
</div>
<div>
    <?= Html::activeHiddenInput($model, 'payload') ?>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end() ?>
<?php
$data = $model->payload ?? 'null';
$this->registerJs(<<<JS
(function() {

    function getNextNode(node) {
        var next = node.firstChild;
        if (next) {
            return next;
        }
        while (node) {
            if ( (next = node.nextSibling) ) {
                return next;
            }
            node = node.parentNode;
        }
    }

    function insertAfter(node, precedingNode) {
        var nextNode = precedingNode.nextSibling, parent = precedingNode.parentNode;
        if (nextNode) {
            parent.insertBefore(node, nextNode);
        } else {
            parent.appendChild(node);
        }
        return node;
    }

    function getNodesInRange(range) {
        var start = range.startContainer;
        var end = range.endContainer;
        var commonAncestor = range.commonAncestorContainer;
        var nodes = [];
        var node;

        // Walk parent nodes from start to common ancestor
        for (node = start.parentNode; node; node = node.parentNode) {
            nodes.push(node);
            if (node === commonAncestor) {
                break;
            }
        }
        nodes.reverse();

        // Walk children and siblings from start until end is found
        for (node = start; node; node = getNextNode(node)) {
            nodes.push(node);
            if (node === end) {
                break;
            }
        }

        return nodes;
    }

    function getTextNodesInRange(range) {
        var textNodes = [];
        var nodes = getNodesInRange(range);
        for (var i = 0, node, el; node = nodes[i++]; ) {
            if (node.nodeType === 3) {
                textNodes.push(node);
            }
        }
        return textNodes;
    }

    function isCharacterDataNode(node) {
        var t = node.nodeType;
        return t === 3 || t === 4 || t === 8 ; // Text, CDataSection or Comment
    }

    function splitDataNode(node, index) {
        var newNode = node.cloneNode(false);
        newNode.deleteData(0, index);
        node.deleteData(index, node.length - index);
        insertAfter(newNode, node);
        return newNode;
    }

    function getNodeIndex(node) {
        var i = 0;
        while ( (node = node.previousSibling) ) {
            ++i;
        }
        return i;
    }

    function splitRangeBoundaries(range) {
        var sc = range.startContainer, so = range.startOffset, ec = range.endContainer, eo = range.endOffset;
        var startEndSame = (sc === ec);

        // Split the end boundary if necessary
        if (isCharacterDataNode(ec) && eo > 0 && eo < ec.length) {
            splitDataNode(ec, eo);
        }

        // Split the start boundary if necessary
        if (isCharacterDataNode(sc) && so > 0 && so < sc.length) {
            sc = splitDataNode(sc, so);
            if (startEndSame) {
                eo -= so;
                ec = sc;
            } else if (ec === sc.parentNode && eo >= getNodeIndex(sc)) {
                ++eo;
            }
            so = 0;
        }
        range.setStart(sc, so);
        range.setEnd(ec, eo);
    }

    function surroundRangeContents(range, templateElement, afterInsertCallback) {
        splitRangeBoundaries(range);
        var textNodes = getTextNodesInRange(range);
        if (textNodes.length === 0) {
            return;
        }
        for (var i = 0, node, el; node = textNodes[i++]; ) {
            if (node.nodeType === 3) {
                el = templateElement.cloneNode(true);
                node.parentNode.insertBefore(el, node);
                afterInsertCallback(el, node);
            }
        }
        range.setStart(textNodes[0], 0);
        var lastTextNode = textNodes[textNodes.length - 1];
        range.setEnd(lastTextNode, lastTextNode.length);
    }

    function generateUUID() {
        var d = new Date().getTime();
        var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16;
            if (d > 0) {
                r = (d + r) % 16 | 0;
                d = Math.floor(d / 16);
            }
            else {
                r = (d2 + r) % 16 | 0;
                d2 = Math.floor(d2 / 16);
            }
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    var data = $data;

    document.getElementById('content').addEventListener('paste', function(e) {
        e.preventDefault();
        var text = (e.originalEvent || e).clipboardData.getData('text/plain');
        document.execCommand("insertHTML", false, text);
    });

    function Fragments(data) {

        var values = {
            'content' : '',
            'fragments': []
        };
        if (data) {
            values = data;
        }

        this.createFragment = (id) => {
            values.fragments.push({id, items: []});
            return id;
        }

        this.findFragment = (id) => {
            return values.fragments.filter((fragment) => fragment.id === id)[0];
        };
        this.findFragmentItem = (fragment_id, item_id) => {
            const fragment = this.findFragment(fragment_id);
            return fragment.items.filter(item => {
                return item.id === item_id;
            })[0];
        };

        this.createFragmentItem = (id, newItem) => {
            const fragment = this.findFragment(id);
            const item = {
                ...newItem,
                order: values.fragments.length + 1
            };
            fragment.items.push(item);
            return item;
        }

        this.getFragments = () => values.fragments;
        this.getFragmentItems = (id) => {
            const fragment = this.findFragment(id);
            return fragment.items;
        };

        this.getFragmentCorrectItem = (id) => {
            const fragment = this.findFragment(id);
            return fragment.items.filter(item => {
                return item.correct;
            })[0];
        };

        this.setFragmentCorrectItem = (fragment_id, item_id) => {
            const fragment = this.findFragment(fragment_id);
            fragment.items.map((item) => {
                item.correct = false;
            });
            const item = fragment.items.filter(item => {
                return item.id === item_id;
            })[0];
            item.correct = true;
            return item;
        };

        this.removeFragmentItem = (fragment_id, item_id) => {
            const fragment = this.findFragment(fragment_id);
            fragment.items = fragment.items.filter(item => {
                return item.id !== item_id;
            });
        }

        this.getContent = () => values.content;
        this.setContent = (content) => values.content = content;

        this.getPayload = () => values;
    }

    const dataWrapper = new Fragments(data);

    var content = dataWrapper.getContent();
    dataWrapper.getFragments().forEach(function(fragment) {

        var code = $('<span/>', {
            'class': 'dropdown',
            'contenteditable': false,
            'data-fragment-id': fragment.id
        });
        const correctItem = dataWrapper.getFragmentCorrectItem(fragment.id);
        let title = 'ПУСТО';
        if (correctItem) {
            title = correctItem.title;
        }
        code.append('<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown">' + title + '</button><ul class="dropdown-menu"></ul>');

        var reg = new RegExp('{' + fragment.id + '}');
        content = content.replace(reg, code[0].outerHTML);
    });
    if (content.length === 0) {
        content = '<div><br></div>';
    }
    $('#content').html(content);

    const observer = new MutationObserver((mutationRecords) => {
        const content = $('#content').html();
        const el = $('<div>' + content + '</div>');
        el.find('span[data-fragment-id]').replaceWith(function() {
            return '{' + $(this).attr('data-fragment-id') + '}';
        });
        dataWrapper.setContent(el[0].innerHTML);
    });
    observer.observe($('#content')[0], {
        subtree: true,
        characterData: true
    });

    function trimRanges(selection) {
        for (let i = 0, range = selection.getRangeAt(0); i < selection.rangeCount; range = selection.getRangeAt(i++)) {

            const text = selection.toString();
            const startOffset = text.length - text.trimStart().length;
            const endOffset = text.length - text.trimEnd().length;

            if (startOffset) {
                const offset = range.startOffset + startOffset;
                if (offset < 0) {
                    // If the range will underflow the current element, then it belongs in the previous element
                    const start = range.startContainer.parentElement.previousSibling;
                    range.setStart(start, start.textContent.length + offset);
                } else if (offset > range.startContainer.textContent.length) {
                    // If the range will overflow the current element, then it belongs in the next element
                    const start = range.startContainer.parentElement.nextSibling;
                    range.setStart(start, offset - range.startContainer.textContent.length);
                } else {
                    range.setStart(range.startContainer, offset);
                }
            }
            if (endOffset) {
                const offset = range.endOffset - endOffset;
                if (offset < 0) {
                    // If the range will underflow the current element, then it belongs in the previous element
                    const end = range.endContainer.parentElement.previousSibling;
                    range.setEnd(end, end.textContent.length + offset);
                } else if (offset > range.endContainer.textContent.length) {
                    // If the range will overflow the current element, then it belongs in the next element
                    const end = range.endContainer.parentElement.nextSibling;
                    range.setEnd(end, offset - range.endContainer.textContent.length);
                } else {
                    range.setEnd(range.endContainer, offset);
                }
            }
        }
    }

    $('#add').on('click', function() {
        if (window.getSelection) {
            var sel = window.getSelection();
            if (sel.rangeCount > 0) {

                trimRanges(sel);

                var templateElement = document.createElement("span");
                templateElement.className = "dropdown";
                templateElement.setAttribute('contenteditable', false);
                templateElement.innerHTML = '<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>';

                var ranges = [];
                var range;
                for (var i = 0, len = sel.rangeCount; i < len; ++i) {
                    ranges.push( sel.getRangeAt(i) );
                }
                sel.removeAllRanges();

                i = ranges.length;
                while (i--) {
                    range = ranges[i];
                    surroundRangeContents(range, templateElement, function(element, textNode) {

                        const id = dataWrapper.createFragment(generateUUID());
                        element.setAttribute('data-fragment-id', id);

                        dataWrapper.createFragmentItem(id, {
                            id: generateUUID(),
                            title: textNode.textContent,
                            correct: true
                        });

                        element.querySelector('.dropdown-toggle').appendChild(textNode);
                    });
                    sel.addRange(range);
                }
            }
        }
    });

    function fragmentItemTextChanged(fragment_id, item_id, text) {
        const item = dataWrapper.findFragmentItem(fragment_id, item_id);
        if (item.correct) {
            $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text(text);
        }
        item.title = text;
    }

    function fragmentItemSetCorrect(fragment_id, item_id) {
        const item = dataWrapper.setFragmentCorrectItem(fragment_id, item_id);
        $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text(item.title);
    }

    function fragmentItemRemove(elem, fragment_id, item_id) {

        const item = dataWrapper.findFragmentItem(fragment_id, item_id);
        if (item.correct) {
            $('#content').find('span[data-fragment-id=' + fragment_id + '] > .highlight').text('ПУСТО');
        }

        dataWrapper.removeFragmentItem(fragment_id, item_id);
        $(elem).parent().parent().hide().remove();
    }

    function addFragmentItem(fragment_id) {

        const item = dataWrapper.createFragmentItem(fragment_id, {
            id: generateUUID(),
            correct: false,
            title: ''
        });

        const elem = createFragmentItemElement(fragment_id, item);

        const createElem = $('#content')
            .find('span[data-fragment-id=' + fragment_id + '] > .dropdown-menu .divider');

        elem.insertBefore(createElem)
            .find('.fragment-title__edit')
            .focus();
    }

    function createFragmentItemElement(fragment_id, item) {
        return $('<li/>', {class: 'fragment-item'})
            .append(
                $('<span/>', {class: 'fragment-input'})
                    .append(
                        $('<input/>', {
                            name: fragment_id,
                            type: 'radio',
                            checked: item.correct
                        })
                            .on('click', function() {
                                fragmentItemSetCorrect(fragment_id, item.id);
                            })
                    )
            )
            .append(
                $('<span/>', {'class': 'fragment-title'})
                    .append(
                        $('<a/>', {'href': '#', 'contenteditable': true, class: 'fragment-title__edit'})
                            .on('input', function() {
                                fragmentItemTextChanged(fragment_id, item.id, $(this).text());
                            })
                            .on('keydown', function(event) {
                                if (event.key === 'Enter') {
                                    event.preventDefault();
                                    return;
                                }
                                if (event.key !== ' ') {
                                    return;
                                }
                                event.preventDefault();
                                document.execCommand("insertText", false, ' ');
                            })
                            .text(item.title)
                    )
            )
            .append(
                $('<span/>', {class: 'fragment-action'})
                    .append(
                        $('<a/>', {'href': '#'})
                            .html('<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>')
                            .on('click', function(e) {
                                e.preventDefault();
                                fragmentItemRemove(this, fragment_id, item.id);
                                return false;
                            })
                    )
            );
    }

    $('#content')
        .on('hide.bs.dropdown', '.dropdown', function() {
            $(this).find('.dropdown-menu').empty();
        })
        .on('show.bs.dropdown', '.dropdown', function() {

            const fragment_id = $(this).attr('data-fragment-id');
            const menu = $(this).find('.dropdown-menu');
            menu.empty();

            dataWrapper.getFragmentItems(fragment_id).forEach(function(item) {
                const elem = createFragmentItemElement(fragment_id, item);
                elem.appendTo(menu);
            });

            $('<li/>', {class: 'divider', role: 'separator'})
                .appendTo(menu);

            $('<li/>').append(
                $('<a/>', {'href': '#', 'class': 'add-word'})
                    .text('Добавить слово')
                    .on('click', function(e) {
                        e.preventDefault();
                        addFragmentItem(fragment_id);
                    })
            ).appendTo(menu);

            $('<li/>').append(
                $('<a/>', {'href': '#', 'class': 'add-word'})
                    .text('Удалить пропуск')
                    .on('click', function(e) {
                        e.preventDefault();

                    })
            ).appendTo(menu);
        });

    $('#content').on('click', '.dropdown-menu', function(e) {
        e.stopPropagation();
    });

    const form = $('#pass-test-form');

    form.on('beforeValidate', function() {

        $('#createpasstestform-content').val($('#content').text());

        const el = $('<div>' + $('#content').html() + '</div>');
        el.find('span[data-fragment-id]').replaceWith(function() {
            return '{' + $(this).attr('data-fragment-id') + '}';
        });

        const content = el[0].innerHTML;

        const fragments = [];
        $('#content').find('[data-fragment-id]').each(function(index, elem) {
            const fragmentId = elem.getAttribute('data-fragment-id');
            const fragment = dataWrapper.findFragment(fragmentId);
            if (fragment) {
                fragments.push(fragment);
            }
        });

        const payload = dataWrapper.getPayload();
        payload.content = content;
        payload.fragments = fragments;
        $('#createpasstestform-payload').val(JSON.stringify(payload));
    });

    function yiiFormSubmit(form, beforeCallback) {

      function btnLoading(elem) {
        $(elem).attr("data-original-text", $(elem).html());
        $(elem).prop("disabled", true);
        $(elem).html('<i class="spinner-border spinner-border-sm"></i> Loading...');
      }

      function btnReset(elem) {
        $(elem).prop("disabled", false);
        $(elem).html($(elem).attr("data-original-text"));
      }

      form.on('beforeSubmit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type=submit]');
        btnLoading(btn);
        if (typeof beforeCallback === 'function') {
          beforeCallback(this).always(function () {
            btnReset(btn);
          });
        }
        return false;
      })
        .on('submit', function(e) {
          e.preventDefault();
        });
    }

    const submitCallback = function(form) {

        const formData = new FormData(form);

        return $.ajax({
            url: $(form).attr('action'),
            type: $(form).attr('method'),
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
        .done(doneCallback)
        .fail(failCallback);
    }

    const doneCallback = function(response) {
        if (response && response.success) {
            if (response.url) {
                location.replace(response.url);
            }
            else {
                toastr.success('Успешно');
            }
        }
        else {
            toastr.error(response['error'] || 'Неизвестная ошибка');
        }
    };

    const failCallback = function(response) {
        toastr.error(response.responseJSON.message);
    }

    yiiFormSubmit(form, submitCallback);
})();
JS
);
