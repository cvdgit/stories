
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

(function() {

  const dataWrapper = window.dataWrapper;

  $('#add').on('click', function(e) {
    e.preventDefault();

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

            if (textNode.textContent === ' ') {
              textNode.textContent = '\u00A0';
            }

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

  const templateElement = document.createElement("span");
  templateElement.className = "dropdown";
  templateElement.setAttribute('contenteditable', false);
  templateElement.innerHTML = '<button class="btn btn-default dropdown-toggle highlight" data-toggle="dropdown"></button><ul class="dropdown-menu"></ul>';

  $('#add-multi').on('click', function(e) {
    e.preventDefault();

    if (!window.getSelection) {
      toastr.error('window.getSelection error');
      return;
    }

    const selection = window.getSelection();
    if (selection.toString().length === 0) {
      toastr.info('Необходимо выделить фрагмент текста');
      return;
    }

    if (selection.isCollapsed) {
      return;
    }

    const selText = selection.toString();
    const skipTrim = (selText.length === 1) && (selText === ' ');
    if (!skipTrim) {
      trimRanges(selection);
    }

    const ranges = [];
    for (let i = 0, len = selection.rangeCount; i < len; ++i) {
      ranges.push(selection.getRangeAt(i));
    }
    selection.removeAllRanges();

    let range;
    i = ranges.length;
    const elementId = generateUUID();
    while (i--) {

      range = ranges[i];

      surroundRangeContents(range, templateElement, function(element, textNode) {

        dataWrapper.createFragment(elementId, true);
        element.setAttribute('data-fragment-id', elementId);

        if (textNode.textContent === ' ') {
          textNode.textContent = '\u00A0';
        }

        const words = textNode.textContent.replace(/\s\s+/g, ' ').split(' ');
        words.forEach(word => {

          dataWrapper.createFragmentItem(elementId, {
            id: generateUUID(),
            title: word.trim(),
            correct: true
          });
        });

        textNode.textContent = words.join(', ');

        element.querySelector('.dropdown-toggle').appendChild(textNode);
      });

      selection.addRange(range);
    }
  });

})();
