function MentalMapsAi() {

  async function sendMessage(payload, onMessage, onEndCallback) {
    let accumulatedMessage = ''
    return sendEventSourceMessage({
      url: '/admin/index.php?r=gpt/mental-map/text-fragments',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'text/event-stream',
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content')
      },
      body: JSON.stringify(payload),
      onMessage: (streamedResponse) => {
        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }
        onMessage(accumulatedMessage)
      },
      onError: (streamedResponse) => {
        console.log(streamedResponse)
      },
      onEnd: () => onEndCallback(accumulatedMessage)
    })
  }

  this.createMentalMaps = (text, endCallback) => {
    sendMessage({text}, () => {
    }, endCallback)
  }

  this.processFragment = (textFragment) => {
    return createWordItem(textFragment, '123')
  }

  let hideWord = false

  function hideWords(nodeList, words, even = true) {
    let counter = 0;
    for (let i = 0, curEl, nextEl; i < nodeList.length; i++) {

      curEl = nodeList[i]
      nextEl = nodeList[i + 1]

      /*if (curEl.classList.contains('selected') || curEl.innerText === '-') {
        hideWord = false
        continue
      }
      if (hideWord === false) {
        hideWord = true
        continue
      }*/

      if (/*hideWord && */curEl.innerText.length > 1) {
        if (curEl.classList.contains('selected') || nextEl?.classList.contains('selected')) {
          hideWord = false
        } else {

          counter++
          if (even && counter % 2 !== 0) {
            continue
          }
          if (even === false && counter % 2 === 0) {
            continue
          }

          curEl.classList.add('selected')
          const id = curEl.dataset.wordId

          const word = words.find(w => w.id === id)
          word.hidden = true

          hideWord = false
        }
      }
    }
  }

  this.hideWordsOdd = (words) => {
    const el = document.createElement('div')
    appendWordElements(words, el)
    hideWord = true
    hideWords(el.querySelectorAll('.text-item-word'), words, false)
    return getTextBySelections(words)
  }

  this.hideWordsEven = (words) => {
    const el = document.createElement('div')
    appendWordElements(words, el)
    hideWord = false
    hideWords(el.querySelectorAll('.text-item-word'), words)
    return getTextBySelections(words)
  }

  function initBoxes(mentalMapsOrder) {
    const $mapsContainer = $('<div class="content-mm-maps"/>');
    $mapsContainer.append('<div style="font-weight: 500; font-size: 18px">Создать:<div/>');
    mentalMapsOrder.map(({type, title}) => {
      const $item = $(`
<div data-content-type="${type}" class="content-mm-maps-item" style="flex-direction: row; justify-content: space-between">
<label><input class="content-item-selected" type="checkbox"> ${title}</label>
<label class="content-item-required-wrap"><input class="content-item-required" type="checkbox"> Обязательно</label>
</div>
`);
      $mapsContainer.append($item);
    });
    return $mapsContainer[0];
  }

  function createFragmentEditor(container, options = {}) {

    let enterTimer = null;
    let destroyed = false;

    const config = {
      debounceMs: 120,
      ...options
    };

    container.classList.add("text-content");

    if (!container.querySelector(".fragment")) {
      container.appendChild(createFragment(""));
    }

    function createFragment(text = "") {
      const div = document.createElement("div");
      div.className = "fragment";
      div.contentEditable = 'plaintext-only';
      div.innerText = text;
      return div;
    }

    function getFragment(node) {
      while (node && node !== container) {
        if (node.classList?.contains("fragment")) return node;
        node = node.parentNode;
      }
      return null;
    }

    function setCaretStart(el) {
      const r = document.createRange();
      const s = window.getSelection();
      r.selectNodeContents(el);
      r.collapse(true);
      s.removeAllRanges();
      s.addRange(r);
    }

    function setCaretEnd(el) {
      const r = document.createRange();
      const s = window.getSelection();
      r.selectNodeContents(el);
      r.collapse(false);
      s.removeAllRanges();
      s.addRange(r);
    }

    function debounce(fn, delay) {
      let t;
      return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), delay);
      };
    }

    const cleanup = debounce(() => {
      if (destroyed) return;

      const fragments = Array.from(container.querySelectorAll(".fragment"));

      fragments.forEach(f => {
        if (!f.innerText.trim()) {

          if (container.children.length === 1) return;

          const sel = window.getSelection();
          if (sel.rangeCount && f.contains(sel.anchorNode)) return;

          f.remove();
        }
      });

      if (!container.querySelector(".fragment")) {
        container.appendChild(createFragment(""));
      }

    }, config.debounceMs);

    function onKeyDown(e) {

      if (destroyed) return;

      if (e.key !== "Enter" && e.key !== "Backspace") return;

      const sel = window.getSelection();
      if (!sel.rangeCount) return;

      const range = sel.getRangeAt(0);
      const fragment = getFragment(range.startContainer);
      if (!fragment) return;

      if (e.key === "Enter") {

        if (enterTimer) {
          clearTimeout(enterTimer);
          enterTimer = null;

          e.preventDefault();

          const sel = window.getSelection();
          if (!sel.rangeCount) return;

          const range = sel.getRangeAt(0);

          const fragment = getFragment(range.startContainer);
          if (!fragment) return;

          const beforeRange = range.cloneRange();
          beforeRange.selectNodeContents(fragment);
          beforeRange.setEnd(range.startContainer, range.startOffset);

          const afterRange = range.cloneRange();
          afterRange.selectNodeContents(fragment);
          afterRange.setStart(range.startContainer, range.startOffset);

          const before = beforeRange.extractContents();
          const after = afterRange.extractContents();

          fragment.innerHTML = "";
          fragment.appendChild(before);

          const newFrag = createFragment("");
          newFrag.appendChild(after);

          fragment.after(newFrag);

          setCaretStart(newFrag);

          cleanup();
          return;
        }

        enterTimer = setTimeout(() => {
          enterTimer = null;
        }, 250);

        return;
      }

      if (e.key === "Delete") {

        const sel = window.getSelection();
        if (!sel.rangeCount) return;

        const range = sel.getRangeAt(0);
        const fragment = getFragment(range.startContainer);
        if (!fragment) return;

        const isEnd =
          range.startOffset === (range.startContainer.length ?? 0) &&
          (
            range.startContainer === fragment ||
            fragment.contains(range.startContainer)
          );

        const atLogicalEnd =
          sel.isCollapsed &&
          sel.anchorNode &&
          fragment &&
          sel.anchorNode === fragment &&
          sel.anchorOffset === fragment.childNodes.length;

        if (!isEnd && !atLogicalEnd) return;

        const next = fragment.nextElementSibling;
        if (!next) return;

        e.preventDefault();

        const currentText = fragment.innerText;
        const nextText = next.innerText;

        const mergeText = currentText + nextText;
        fragment.innerText = mergeText;
        next.remove();

        requestAnimationFrame(() => {

          const sel2 = window.getSelection();
          const range2 = document.createRange();

          let offset = currentText.length;

          const walker = document.createTreeWalker(
            fragment,
            NodeFilter.SHOW_TEXT
          );

          let node = null;

          while (walker.nextNode()) {
            const n = walker.currentNode;

            if (offset <= n.length) {
              node = n;
              break;
            }

            offset -= n.length;
          }

          if (node) {
            range2.setStart(node, currentText.length);
          } else {
            range2.selectNodeContents(fragment);
            range2.collapse(false);
          }

          sel2.removeAllRanges();
          sel2.addRange(range2);
        });

        cleanup();
      }
    }

    let backspacePressedOnce = false;

    container.addEventListener("keydown", (e) => {

      if (e.key !== "Backspace") {
        backspacePressedOnce = false;
        return;
      }

      const sel = window.getSelection();
      if (!sel.rangeCount) return;

      const range = sel.getRangeAt(0);
      const fragment = getFragment(range.startContainer);
      if (!fragment) return;

      // нормальная проверка "в начале"
      const isAtStart =
        range.startOffset === 0 &&
        (
          range.startContainer === fragment ||
          range.startContainer.parentNode === fragment
        );

      const prev = fragment.previousElementSibling;

      if (!isAtStart || !prev) {
        backspacePressedOnce = false;
        return;
      }

      // =========================
      // 1. первый Backspace → только позиционирование поведения браузера
      // =========================
      if (!backspacePressedOnce) {
        backspacePressedOnce = true;
        return; // ничего не делаем → браузер просто "показывает intent"
      }

      // =========================
      // 2. второй Backspace → merge
      // =========================
      e.preventDefault();

      backspacePressedOnce = false;

      const prevText = prev.innerText;
      const currentText = fragment.innerText;

      const mergeOffset = prevText.length;

      // merge DOM
      prev.innerText = prevText + currentText;
      fragment.remove();

      // =========================
      // caret СТАВИМ ПОСЛЕ DOM STABLE
      // =========================
      requestAnimationFrame(() => {

        const sel2 = window.getSelection();
        const range2 = document.createRange();

        let node = null;
        let offset = mergeOffset;

        const walker = document.createTreeWalker(prev, NodeFilter.SHOW_TEXT);

        while (walker.nextNode()) {
          const n = walker.currentNode;

          if (offset <= n.length) {
            node = n;
            break;
          }

          offset -= n.length;
        }

        if (node) {
          range2.setStart(node, offset);
          range2.collapse(true);
        } else {
          range2.selectNodeContents(prev);
          range2.collapse(false);
        }

        sel2.removeAllRanges();
        sel2.addRange(range2);
      });

      cleanup();
    });

    function textAsFragments(text) {
      return text
        .split(/\n{2,}/)
        .map(t => t.trim())
        .filter(Boolean);
    }

    function onPaste(e) {

      if (destroyed) return;

      e.preventDefault();

      const text = (e.clipboardData || window.clipboardData).getData("text");

      const parts = textAsFragments(text);

      if (!parts.length) return;

      const sel = window.getSelection();
      if (!sel.rangeCount) return;

      const range = sel.getRangeAt(0);
      const fragment = getFragment(range.startContainer);

      if (!fragment) {
        container.innerHTML = "";
        parts.forEach(p => container.appendChild(createFragment(p)));
        return;
      }

      fragment.innerText = parts[0];

      let current = fragment;

      for (let i = 1; i < parts.length; i++) {
        const f = createFragment(parts[i]);
        current.after(f);
        current = f;
      }

      setCaretEnd(current);
      cleanup();
    }

    function onInput() {
      if (destroyed) return;
      cleanup();
    }

    container.addEventListener("keydown", onKeyDown);
    container.addEventListener("paste", onPaste);
    container.addEventListener("input", onInput);

    function getValue() {
      return Array.from(container.querySelectorAll(".fragment"))
        .map(f => f.innerText.trim())
        .filter(Boolean);
    }

    function setValue(arr) {
      container.innerHTML = "";

      (arr || []).forEach(text => {
        container.appendChild(createFragment(text));
      });

      if (!arr || !arr.length) {
        container.appendChild(createFragment(""));
      }
    }

    function focus(index = 0) {
      const f = container.children[index];
      if (!f) return;
      setCaretEnd(f);
    }

    function destroy() {
      destroyed = true;

      container.removeEventListener("keydown", onKeyDown);
      container.removeEventListener("paste", onPaste);
      container.removeEventListener("input", onInput);
    }

    return {
      getValue,
      setValue,
      focus,
      destroy
    };
  }

  /**
   * @param {HTMLElement} container
   * @param mentalMapsOrder
   * @param {Array} texts
   * @param {Number} currentSlideId
   * @param {() => {}} onCreateHandler
   */
  this.show = (container, mentalMapsOrder, texts, currentSlideId, onCreateHandler) => {

    function init() {
      const editor = createFragmentEditor(container.querySelector('#content-editor'));
      editor.setValue(texts);

      container
        .querySelector('.ai-mental-maps-order')
        .appendChild(
          initBoxes(mentalMapsOrder)
        );

      container
        .querySelector('#ai-maps-create')
        .addEventListener('click', async e => {

          const fragments = editor
            .getValue()
            .map(f => f.trim())
            .filter(Boolean);
          if (!fragments.length) {
            toastr.warning('Нет фрагментов');
            return;
          }

          const toCreateMaps = $(container)
            .find('.content-mm-maps-item .content-item-selected:checked')
            .map((i, el) => ({
              title: $(el).parent().text().trim(),
              type: $(el).parents('.content-mm-maps-item').attr('data-content-type'),
              fragments: [],
              required: $(el).parents('.content-mm-maps-item').find('.content-item-required').is(':checked')
            }))
            .get();

          if (!toCreateMaps.length) {
            toastr.warning('Нужно выбрать ментальные карты');
            return;
          }

          const $btn = $(e.target);
          modalHelper.btnLoading($btn);

          const mapResponse = await processContentMentalMaps(
            toCreateMaps,
            fragments.map(text => {
              const id = crypto.randomUUID();
              return {
                id,
                title: text,
                words: createWordItem(text, id).words
              }
            })
          );

          const formData = new FormData();
          formData.append('mentalMaps', mapResponse.mentalMaps);
          formData.append('currentSlideId', currentSlideId);
          formData.append('text', mapResponse.text);

          formHelper.sendForm('/admin/index.php?r=editor/mental-map/create-ai-handler', 'POST', formData)
            .done(response => {
              if (response && response.success) {
                onCreateHandler();
                toastr.success('Успешно');
                return;
              }
              toastr.error(response?.message || 'Произошла ошибка')
            })
        });
    }

    setTimeout(init, 300);
  }
}
