<?php

declare(strict_types=1);

use backend\models\drag_words\UpdateDragWordsForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use backend\models\drag_words\CreateDragWordsForm;

/**
 * @var CreateDragWordsForm|UpdateDragWordsForm $model
 * @var bool $isNewRecord
 */

$this->registerCss(
    <<<CSS
.highlight {
    user-select: none;
}
.content:focus-visible {
    outline: none;
}
.content {
    border: 1px #d0d0d0 solid;
    padding: 10px;
    min-height: 250px;
    line-height: 2.3;
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
.add-words-list {
    min-height: 70px;
    max-height: 110px;
    overflow-y: auto;
    border: 1px #d0d0d0 solid;
    padding: 10px;
    line-height: 2.3;
}
.add-words-list > span {
    display: inline-block;
    margin-right: 10px;
}
.add-words-list .add-word-del {
    position: relative;
    width: 24px;
    height: 24px;
}
.add-words-list .add-word-del svg {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    color: #a94442;
}
CSS
);
?>
<?php
$form = ActiveForm::begin(['id' => 'drag-words-form']) ?>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'imageFile')->fileInput() ?>
<?php
if (!$isNewRecord && $model->haveImage()): ?>
    <div style="padding: 20px 0; text-align: center">
        <?= Html::img($model->getImageUrl(), ['style' => 'max-width: 330px']) ?>
        <div>
            <?= Html::a('Удалить изображение', ['question/delete-image', 'id' => $model->getModelId()]) ?>
        </div>
    </div>
<?php endif ?>
    <div>
        <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
            <div class="content__title">
                <?= Html::activeLabel($model, 'content') ?>
            </div>
            <div style="margin-left:auto">
                <button class="btn btn-primary btn-sm" id="add" type="button">Вставить пропуск</button>
            </div>
        </div>
        <div style="min-height:250px;max-height:250px;overflow-y:auto;margin-bottom:20px">
            <div class="content" id="content" contenteditable="true"></div>
        </div>
        <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
            <div class="content__title">
                <label for="">Дополнительные слова</label>
            </div>
        </div>
        <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
            <div style="width: 100%; margin-right: 10px">
                <input id="additional-word-input" type="text" autocomplete="off" class="form-control">
            </div>
            <div>
                <button type="button" id="add-word" class="btn btn-secondary">Добавить</button>
            </div>
        </div>
        <div id="additional-words" class="add-words-list"></div>
        <?= $form->field($model, 'content')->hiddenInput(["id" => "content-field"])->label(false) ?>
    </div>
    <div>
        <?= Html::activeHiddenInput($model, 'payload', ["id" => "payload-field"]) ?>
        <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']
        ) ?>
    </div>
<?php
ActiveForm::end() ?>
<?php
$data = $model->payload ?? 'null';
$this->registerJs(
    <<<JS
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
        const text = (e.originalEvent || e).clipboardData.getData('text/plain');
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

        this.createFragment = (id, title, correct) => {
            values.fragments.push({id, title, correct});
            return id;
        }

        this.findFragment = (id) => {
            return values.fragments.filter((fragment) => fragment.id === id)[0];
        };

        this.getFragments = () => values.fragments;

        this.getContent = () => values.content;
        this.setContent = (content) => values.content = content;

        this.getPayload = () => values;
    }

    const dataWrapper = new Fragments(data);

    const additionalWords = $('#additional-words');

    var content = dataWrapper.getContent();
    dataWrapper.getFragments().forEach(function(fragment) {

        if (fragment.correct) {

            const code = $('<span/>', {
                'contenteditable': false,
                'data-fragment-id': fragment.id
            });
            code.append('<button type="button" class="btn btn-default highlight">' + fragment.title + '</button>');

            const reg = new RegExp('{' + fragment.id + '}');
            content = content.replace(reg, code[0].outerHTML);
        }
        else {
            const element = createAdditionalWordElement(fragment.title);
            element.setAttribute('data-fragment-id', fragment.id);
            additionalWords.append(element);
        }
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

    function createFragmentElement() {
        const templateElement = document.createElement("span");
        templateElement.setAttribute('contenteditable', false);
        templateElement.innerHTML = '<button type="button" class="btn btn-default highlight"></button>';
        return templateElement;
    }

    function createAdditionalWordElement(text) {

        const element = createFragmentElement();
        element.querySelector('.highlight').textContent = text;

        $(element).append(
            $('<a/>', {
                'class': 'btn btn-link add-word-del',
                'href': '#',
                'html': '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>'
            })
            .on('click', function(e) {
                e.preventDefault();
                $(this).parent().fadeOut(500).after($(this).parent().remove());
            })
        );
        return element;
    }

    $('#add').on('click', function() {
        if (window.getSelection) {
            var sel = window.getSelection();
            if (sel.rangeCount > 0) {

                if (sel.isCollapsed) {
                    return;
                }

                const selText = sel.toString();
                const skipTrim = (selText.length === 1) && (selText === ' ');
                if (!skipTrim) {
                    trimRanges(sel);
                }

                const templateElement = createFragmentElement();

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

                        if (textNode.textContent === ' ') {
                            textNode.textContent = '\u00A0';
                        }

                        const id = dataWrapper.createFragment(generateUUID(), textNode.textContent, true);
                        element.setAttribute('data-fragment-id', id);

                        element.querySelector('.highlight').appendChild(textNode);
                    });
                    sel.addRange(range);
                }
            }
        }
    });

    $('#add-word').on('click', function() {

        const word = $('#additional-word-input').val().trim();
        if (!word.length) {
            return;
        }

        const element = createAdditionalWordElement(word);

        const fragmentId = dataWrapper.createFragment(generateUUID(), word, false);
        element.setAttribute('data-fragment-id', fragmentId);

        additionalWords.append(element);
        $('#additional-word-input').val('').focus();
    });

    const form = $('#drag-words-form');

    form.on('beforeValidate', function() {

        $('#content-field').val($('#content').text());

        const el = $('<div>' + $('#content').html() + '</div>');
        el.find('span[data-fragment-id]').replaceWith(function() {
            return '{' + $(this).attr('data-fragment-id') + '}';
        });

        const content = el[0].outerHTML;

        const fragments = [];
        $('#content').find('[data-fragment-id]').each(function(index, elem) {
            const fragmentId = elem.getAttribute('data-fragment-id');
            const fragment = dataWrapper.findFragment(fragmentId);
            if (fragment) {
                fragments.push(fragment);
            }
        });

        additionalWords.find('[data-fragment-id]').each(function(index, elem) {
            const fragmentId = elem.getAttribute('data-fragment-id');
            const fragment = dataWrapper.findFragment(fragmentId);
            if (fragment) {
                fragments.push(fragment);
            }
        });

        const payload = dataWrapper.getPayload();
        payload.content = content;
        payload.fragments = fragments;
        $('#payload-field').val(JSON.stringify(payload));
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
                //toastr.success('Успешно');
                location.reload()
            }
        }
        else {
            toastr.error(response['message'] || 'Неизвестная ошибка');
        }
    };

    const failCallback = function(response) {
        toastr.error(response.responseJSON.message);
    }

    yiiFormSubmit(form, submitCallback);
})();
JS
);
