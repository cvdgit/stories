
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
})();
