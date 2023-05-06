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

  const initSelection = () => {

    if (!window.getSelection) {
      throw new Error('window.getSelection error');
    }

    const selection = window.getSelection();

    if (selection.toString().length === 0) {
      throw new Error('Необходимо выделить фрагмент текста');
    }

    if (selection.isCollapsed) {
      throw new Error('selection is collapsed');
    }

    return selection;
  }

  const initRanges = (selection) => {

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

    return ranges;
  }

  $('#add').on('click', function (e) {
    e.preventDefault();

    const selection = initSelection();
    const ranges = initRanges(selection);

    let i = ranges.length;
    while (i--) {
      const range = ranges[i];

      surroundRangeContents(range, (textNodes) => {

        const element = fragmentElementBuilder('single').cloneNode(true);
        textNodes[0].parentNode.insertBefore(element, textNodes[0]);

        let textContent = '';
        for (let i = 0, node; node = textNodes[i++];) {
          element.appendChild(node);
          textContent += node.nodeType === 3 ? node.textContent : node.outerHTML;
          element.querySelector('.dropdown-toggle').appendChild(node);
        }

        const id = dataWrapper.createFragment(generateUUID());
        element.setAttribute('data-fragment-id', id);

        if (textNodes[0].textContent === ' ') {
          textNodes[0].textContent = '\u00A0';
        }

        dataWrapper.createFragmentItem(id, {
          id: generateUUID(),
          title: textContent,
          correct: true
        });

      });

      selection.addRange(range);
    }
  });

  $('#add-multi').on('click', function (e) {
    e.preventDefault();

    const selection = initSelection();
    const ranges = initRanges(selection);

    let i = ranges.length;
    const elementId = generateUUID();

    while (i--) {

      const range = ranges[i];

      surroundRangeContents(range, function (textNodes) {

        const element = fragmentElementBuilder('multi').cloneNode(true);
        textNodes[0].parentNode.insertBefore(element, textNodes[0]);

        let textContent = '';
        for (let i = 0, node; node = textNodes[i++];) {
          element.appendChild(node);
          textContent += node.nodeType === 3 ? node.textContent : node.outerHTML;
          element.querySelector('.dropdown-toggle').appendChild(node);
        }

        dataWrapper.createFragment(elementId, true);
        element.setAttribute('data-fragment-id', elementId);

        if (textNodes[0].textContent === ' ') {
          textNodes[0].textContent = '\u00A0';
        }

        const words = textContent.replace(/\s\s+/g, ' ').split(' ');
        words.forEach(word => {
          dataWrapper.createFragmentItem(elementId, {
            id: generateUUID(),
            title: word.trim(),
            correct: true
          });
        });

        //textNode.textContent = words.join(', ');
        //element.querySelector('.dropdown-toggle').appendChild(textNode);
      });

      selection.addRange(range);
    }
  });

  $('#add-region').on('click', function (e) {
    e.preventDefault();

    const selection = initSelection();
    const ranges = initRanges(selection);

    let i = ranges.length;
    const elementId = generateUUID();

    while (i--) {

      const range = ranges[i];

      surroundRangeContents(range, function (textNodes) {

        const element = fragmentElementBuilder('region').cloneNode(true);
        textNodes[0].parentNode.insertBefore(element, textNodes[0]);

        let textContent = '';
        for (let i = 0, node; node = textNodes[i++];) {
          element.appendChild(node);
          textContent += node.nodeType === 3 ? node.textContent : node.outerHTML;
          element.querySelector('.highlight').appendChild(node);
        }

        dataWrapper.createRegionFragment(elementId);
        element.setAttribute('data-fragment-id', elementId);

        if (textNodes[0].textContent === ' ') {
          textNodes[0].textContent = '\u00A0';
        }

        dataWrapper.createFragmentItem(elementId, {
          id: generateUUID(),
          title: textContent,
          correct: true
        });
      });

      selection.addRange(range);
    }
  });
})();
