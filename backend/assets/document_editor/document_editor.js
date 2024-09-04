window.DocumentEditor = (function () {

  let pages = []
  const pages_overlay_refs = {}
  let pages_height = 0
  let editor_width = 0
  let prevent_next_content_update_from_parent = false
  let current_text_style = false
  let printing_mode = false

  let content = [
    ''
  ]
  const configDefault = {
    page_format_mm: [384, 216],
    zoom: 1.0,
    display: 'vertical',
    page_margins: '10mm 15mm',
  }
  const config = {...configDefault}

  let editorElement
  let contentElement

  function update_editor_width() {
    editorElement.classList.add("hide_children");
    editor_width = editorElement.clientWidth;
    update_pages_elts();
    editorElement.classList.remove("hide_children");
  }

  function process_current_text_style() {
    let style = false;
    const sel = window.getSelection();
    if (sel.focusNode) {
      const element = sel.focusNode.tagName ? sel.focusNode : sel.focusNode.parentElement;
      if (element && element.isContentEditable) {
        style = window.getComputedStyle(element);

        // compute additional properties
        style.textDecorationStack = []; // array of text-decoration strings from parent elements
        style.headerLevel = 0;
        style.isList = false;
        let parent = element;
        while (parent) {
          const parent_style = window.getComputedStyle(parent);
          // stack CSS text-decoration as it is not overridden by children
          style.textDecorationStack.push(parent_style.textDecoration);
          // check if one parent is a list-item
          if (parent_style.display === 'list-item') style.isList = true;
          // get first header level, if any
          if (!style.headerLevel) {
            for (let i = 1; i <= 6; i++) {
              if (parent.tagName.toUpperCase() === 'H' + i) {
                style.headerLevel = i;
                break;
              }
            }
          }
          parent = parent.parentElement;
        }
      }
    }
    current_text_style = style;
  }

  function emit_new_content() {

    let removed_pages_flag = false; // flag to call reset_content if some pages were removed by the user

    // process the new content
    const new_content = content.map((item, content_idx) => {
      // select pages that correspond to this content item (represented by its index in the array)
      const filterPages = pages.filter(page => (page.content_idx == content_idx));
      // if there are no pages representing this content (because deleted by the user), mark item as false to remove it
      if (!filterPages.length) {
        removed_pages_flag = true;
        return false;
      }
      // if item is a string, concatenate each page content and set that
      else if (typeof item == "string") {
        return filterPages.map(page => {
          // remove any useless <div> surrounding the content
          let elt = page.elt;
          while (elt.children.length == 1 && elt.firstChild.tagName && elt.firstChild.tagName.toLowerCase() == "div" && !elt.firstChild.getAttribute("style")) {
            elt = elt.firstChild;
          }
          return ((elt.innerHTML == "<br>" || elt.innerHTML == "<!---->") ? "" : elt.innerHTML); // treat a page containing a single <br> or an empty comment as an empty content
        }).join('');
      }
      // if item is a component, just clone the item
      else return {template: item.template, props: {...item.props}};
    }).filter(item => (item !== false)); // remove empty items

    // avoid calling reset_content after the parent content is updated (infinite loop)
    if (!removed_pages_flag) prevent_next_content_update_from_parent = true;

    // send event to parent to update the synced content
    //this.$emit("update:content", new_content);
    content = new_content
    handleUpdateContent()
  }

  function handleUpdateContent() {
    if (prevent_next_content_update_from_parent) {
      prevent_next_content_update_from_parent = false;
    } else {
      reset_content();
    }
  }

  function update_pages_elts() {
    // Removing deleted pages
    const deleted_pages = [...contentElement.children].filter((page_elt) => !pages.find(page => (page.elt == page_elt)));
    for (const page_elt of deleted_pages) {
      page_elt.remove();
    }

    // Adding / updating pages
    for (const [page_idx, page] of pages.entries()) {
      // Get either existing page_elt or create it
      if (!page.elt) {
        page.elt = document.createElement("div");
        page.elt.className = "page";
        page.elt.dataset.isVDEPage = "";
        const next_page = pages[page_idx + 1];
        contentElement.insertBefore(page.elt, next_page ? next_page.elt : null);
      }
      // Update page properties
      page.elt.dataset.contentIdx = page.content_idx;
      if (!printing_mode) {
        // (convert page_style to CSS string)
        page.elt.style = Object.entries(page_style(page_idx, page.template ? false : true)).map(([k, v]) => k.replace(/[A-Z]/g, match => ("-" + match.toLowerCase())) + ":" + v).join(';');
      }
      page.elt.contentEditable = 'plaintext-only' // (this.editable && !page.template) ? true : false;
    }
  }

  function page_style(page_idx, allow_overflow) {
    const px_in_mm = 0.2645833333333;
    const page_width = config.page_format_mm[0] / px_in_mm;
    const page_spacing_mm = 10;
    const page_with_plus_spacing = (page_spacing_mm + config.page_format_mm[0]) * config.zoom / px_in_mm;
    const view_padding = 20;
    const inner_width = editor_width - 2 * view_padding;
    let nb_pages_x = 1, page_column, x_pos, x_ofx, left_px, top_mm, bkg_width_mm, bkg_height_mm;
    if (config.display === "horizontal") {
      if (inner_width > (pages.length * page_with_plus_spacing)) {
        nb_pages_x = Math.floor(inner_width / page_with_plus_spacing);
        left_px = inner_width / (nb_pages_x * 2) * (1 + page_idx * 2) - page_width / 2;
      } else {
        nb_pages_x = pages.length;
        left_px = page_with_plus_spacing * page_idx + page_width / 2 * (config.zoom - 1);
      }
      top_mm = 0;
      bkg_width_mm = config.zoom * (config.page_format_mm[0] * nb_pages_x + (nb_pages_x - 1) * page_spacing_mm);
      bkg_height_mm = config.page_format_mm[1] * config.zoom;
    } else { // "grid", vertical
      nb_pages_x = Math.floor(inner_width / page_with_plus_spacing);
      if (nb_pages_x < 1 || config.display === "vertical") nb_pages_x = 1;
      page_column = (page_idx % nb_pages_x);
      x_pos = inner_width / (nb_pages_x * 2) * (1 + page_column * 2) - page_width / 2;
      x_ofx = Math.max(0, (page_width * config.zoom - inner_width) / 2);
      left_px = x_pos + x_ofx;
      top_mm = ((config.page_format_mm[1] + page_spacing_mm) * config.zoom) * Math.floor(page_idx / nb_pages_x);
      const nb_pages_y = Math.ceil(pages.length / nb_pages_x);
      bkg_width_mm = config.zoom * (config.page_format_mm[0] * nb_pages_x + (nb_pages_x - 1) * page_spacing_mm);
      bkg_height_mm = config.zoom * (config.page_format_mm[1] * nb_pages_y + (nb_pages_y - 1) * page_spacing_mm);
    }
    if (page_idx >= 0) {
      const style = {
        position: "absolute",
        left: "calc(" + left_px + "px + " + view_padding + "px)",
        top: "calc(" + top_mm + "mm + " + view_padding + "px)",
        width: config.page_format_mm[0] + "mm",
        // "height" is set below
        padding: (typeof config.page_margins == "function") ? config.page_margins(page_idx + 1, pages.length) : config.page_margins,
        transform: "scale(" + config.zoom + ")"
      };
      style[allow_overflow ? "minHeight" : "height"] = config.page_format_mm[1] + "mm";
      return style;
    } else {
      // Content/background <div> is sized so it lets a margin around pages when scrolling at the end
      return {
        width: "calc(" + bkg_width_mm + "mm + " + (2 * view_padding) + "px)",
        height: "calc(" + bkg_height_mm + "mm + " + (2 * view_padding) + "px)"
      };
    }
  }

  function new_uuid() {
    return Math.random().toString(36).slice(-5)
  }

  let reset_in_progress = false
  function reset_content() {
    // Prevent launching this function multiple times
    if (reset_in_progress) {
      return;
    }
    reset_in_progress = true;

    // If provided content is empty, initialize it first and exit
    if (!content.length) {
      reset_in_progress = false;
      // this.$emit("update:content", [""]);
      return;
    }

    // Delete all pages and set one new page per content item
    pages = content.map((content, content_idx) => ({
      uuid: new_uuid(),
      content_idx,
      template: content.template,
      props: content.props
    }));
    update_pages_elts();

    // Get page height from first empty page
    const first_page_elt = pages[0].elt;
    if (!contentElement.contains(first_page_elt)) {
      contentElement.appendChild(first_page_elt);
    } // restore page in DOM in case it was removed
    pages_height = first_page_elt.clientHeight + 1; // allow one pixel precision

    // Initialize text pages
    for (const page of pages) {

      // set raw HTML content
      if (!content[page.content_idx]) {
        page.elt.innerHTML = "<div><br></div>";
      } // ensure empty pages are filled with at least <div><br></div>, otherwise editing fails on Chrome
      else if (typeof content[page.content_idx] == "string") {
        page.elt.innerHTML = "<div>" + content[page.content_idx] + "</div>";
      }
      /*else if (page.template) {
        const componentElement = defineCustomElement(page.template);
        customElements.define('component-' + page.uuid, componentElement);
        page.elt.appendChild(new componentElement({modelValue: page.props}));
      }*/

      // restore page in DOM in case it was removed
      if (!contentElement.contains(page.elt)) {
        contentElement.appendChild(page.elt);
      }
    }

    // Spread content over several pages if it overflows
    fit_content_over_pages();

    // Remove the text cursor from the content, if any (its position is lost anyway)
    contentElement.blur();

    // Clear "reset in progress" flag
    reset_in_progress = false;
  }

  /**
   * Utility function that acts like an Array.filter on childNodes of "container"
   * @param {HTMLElement} container
   * @param {string} s_tag
   */
  function find_sub_child_sibling_node(container, s_tag) {
    if (!container || !s_tag) return false;
    const child_nodes = container.childNodes;
    for (let i = 0; i < child_nodes.length; i++) {
      if (child_nodes[i].s_tag == s_tag) {
        return child_nodes[i];
      }
    }
    return false;
  }

  /**
   * This function moves every sub-child of argument "child" to the start of the "child_sibling"
   * argument, beginning from the last child, with word splitting and format preserving.
   * Typically, "child" is the current page which content overflows, and "child_sibling" is the
   * next page.
   * @param {HTMLElement} child Element to take children from (current page)
   * @param {HTMLElement} child_sibling Element to copy children to (next page)
   * @param {function} stop_condition Check function that returns a boolean if content doesn't overflow anymore
   * @param {function(HTMLElement):boolean?} do_not_break Optional function that receives the current child element and should return true if the child should not be split over two pages but rather be moved directly to the next page
   * @param {boolean?} not_first_child Should be unset. Used internally to let at least one child in the page
   */
  function move_children_forward_recursively(child, child_sibling, stop_condition, do_not_break, not_first_child) {

    // if the child still has nodes and the current page still overflows
    while (child.childNodes.length && !stop_condition()) {

      // check if page has only one child tree left
      not_first_child = not_first_child || (child.childNodes.length != 1);

      // select the last sub-child
      const sub_child = child.lastChild;

      // if it is a text node, move its content to next page word(/space) by word
      if (sub_child.nodeType == Node.TEXT_NODE) {
        const sub_child_hashes = sub_child.textContent.match(/(\s|\S+)/g);
        const sub_child_continuation = document.createTextNode('');
        child_sibling.prepend(sub_child_continuation);
        const l = sub_child_hashes ? sub_child_hashes.length : 0;
        for (let i = 0; i < l; i++) {
          if (i == l - 1 && !not_first_child) return; // never remove the first word of the page
          sub_child.textContent = sub_child_hashes.slice(0, l - i - 1).join('');
          sub_child_continuation.textContent = sub_child_hashes.slice(l - i - 1, l).join('');
          if (stop_condition()) return;
        }
      }

        // we simply move it to the next page if it is either:
        // - a node with no content (e.g. <img>)
        // - a header title (e.g. <h1>)
        // - a table row (e.g. <tr>)
      // - any element on whose user-custom `do_not_break` function returns true
      else if (!sub_child.childNodes.length || sub_child.tagName.match(/h\d/i) || sub_child.tagName.match(/tr/i) || (typeof do_not_break === "function" && do_not_break(sub_child))) {
        // just prevent moving the last child of the page
        if (!not_first_child) {
          console.log("Move-forward: first child reached with no stop condition. Aborting");
          return;
        }
        child_sibling.prepend(sub_child);
      }

      // for every other node that is not text and not the first child, clone it recursively to next page
      else {
        // check if sub child has already been cloned before
        let sub_child_sibling = find_sub_child_sibling_node(child_sibling, sub_child.s_tag);

        // if not, create it and watermark the relationship with a random tag
        if (!sub_child_sibling) {
          if (!sub_child.s_tag) {
            const new_random_tag = Math.random().toString(36).slice(2, 8);
            sub_child.s_tag = new_random_tag;
          }
          sub_child_sibling = sub_child.cloneNode(false);
          sub_child_sibling.s_tag = sub_child.s_tag;
          child_sibling.prepend(sub_child_sibling);
        }

        // then move/clone its children and sub-children recursively
        move_children_forward_recursively(sub_child, sub_child_sibling, stop_condition, do_not_break, not_first_child);
        sub_child_sibling.normalize(); // merge consecutive text nodes
      }

      // if sub_child was a container that was cloned and is now empty, we clean it
      if (child.contains(sub_child)) {
        if (sub_child.childNodes.length == 0 || sub_child.innerHTML == "") child.removeChild(sub_child);
        else if (!stop_condition()) {
          // the only case when it can be non empty should be when stop_condition is now true
          console.log("sub_child:", sub_child, "that is in child:", child);
          throw Error("Document editor is trying to remove a non-empty sub-child. This "
            + "is a bug and should not happen. Please report a repeatable set of actions that "
            + "leaded to this error to https://github.com/motla/vue-document-editor/issues/new");
        }
      }
    }
  }

  /**
   * This function moves the first element from "next_page_html_div" to the end of "page_html_div", with
   * merging sibling tags previously watermarked by "move_children_forward_recursively", if any.
   * @param {HTMLElement} page_html_div Current page element
   * @param {HTMLElement} next_page_html_div Next page element
   * @param {function} stop_condition Check function that returns a boolean if content overflows
   */
  function move_children_backwards_with_merging(page_html_div, next_page_html_div, stop_condition) {

    // loop until content is overflowing
    while (!stop_condition()) {

      // find first child of next page
      const first_child = next_page_html_div.firstChild;

      // merge it at the end of the current page
      var merge_recursively = (container, elt) => {
        // check if child had been splitted (= has a sibling on previous page)
        const elt_sibling = find_sub_child_sibling_node(container, elt.s_tag);
        if (elt_sibling && elt.childNodes.length) {
          // then dig for deeper children, in case of
          merge_recursively(elt_sibling, elt.firstChild);
        }
        // else move the child inside the right container at current page
        else {
          container.append(elt);
          container.normalize();
        }
      }
      merge_recursively(page_html_div, first_child);
    }
  }

  let fit_in_progress = false
  function fit_content_over_pages() {
    // Data variable this.pages_height must have been set before calling this function
    if (!pages_height) {
      return;
    }

    // Prevent launching this function multiple times
    if (fit_in_progress) {
      return;
    }
    fit_in_progress = true;

    // Check pages that were deleted from the DOM (start from the end)
    for (let page_idx = pages.length - 1; page_idx >= 0; page_idx--) {
      const page = pages[page_idx];

      // if user deleted the page from the DOM, then remove it from this.pages array
      if (!page.elt || !document.body.contains(page.elt)) {
        pages.splice(page_idx, 1);
      }
    }

    // If all the document was wiped out, start a new empty document
    if (!pages.length) {
      fit_in_progress = false; // clear "fit in progress" flag
      // this.$emit("update:content", [""]);
      return;
    }

    // Save current selection (or cursor position) by inserting empty HTML elements at the start and the end of it
    const selection = window.getSelection();
    const start_marker = document.createElement("null");
    const end_marker = document.createElement("null");
    // don't insert markers in case selection fails (if we are editing in components in the shadow-root it selects the page <div> as anchorNode)
    if (selection && selection.rangeCount && selection.anchorNode && !(selection.anchorNode.dataset && selection.anchorNode.dataset.isVDEPage != null)) {
      const range = selection.getRangeAt(0);
      range.insertNode(start_marker);
      range.collapse(false);
      range.insertNode(end_marker);
    }

    // Browse every remaining page
    let prev_page_modified_flag = false;
    for (let page_idx = 0; page_idx < pages.length; page_idx++) { // page length can grow inside this loop
      const page = pages[page_idx];
      let next_page = pages[page_idx + 1];
      let next_page_elt = next_page ? next_page.elt : null;

      // check if this page, the next page, or any previous page content has been modified by the user (don't apply to template pages)
      if (!page.template && (prev_page_modified_flag || page.elt.innerHTML != page.prev_innerHTML
        || (next_page_elt && !next_page.template && next_page_elt.innerHTML != next_page.prev_innerHTML))) {
        prev_page_modified_flag = true;

        // BACKWARD-PROPAGATION
        // check if content doesn't overflow, and that next page exists and has the same content_idx
        if (page.elt.clientHeight <= pages_height && next_page && next_page.content_idx == page.content_idx) {

          // try to append every node from the next page until it doesn't fit
          move_children_backwards_with_merging(page.elt, next_page_elt, () => !next_page_elt.childNodes.length || (page.elt.clientHeight > pages_height));
        }

        // FORWARD-PROPAGATION
        // check if content overflows
        if (page.elt.clientHeight > pages_height) {

          // if there is no next page for the same content, create it
          if (!next_page || next_page.content_idx != page.content_idx) {
            next_page = {uuid: new_uuid(), content_idx: page.content_idx};
            pages.splice(page_idx + 1, 0, next_page);
            update_pages_elts();
            next_page_elt = next_page.elt;
          }

          // move the content step by step to the next page, until it fits
          move_children_forward_recursively(page.elt, next_page_elt, () => (page.elt.clientHeight <= pages_height), () => {
          });
        }

        // CLEANING
        // remove next page if it is empty
        if (next_page_elt && next_page.content_idx == page.content_idx && !next_page_elt.childNodes.length) {
          pages.splice(page_idx + 1, 1);
        }
      }

      // update pages in the DOM
      update_pages_elts();
    }

    // Normalize pages HTML content
    for (const page of pages) {
      if (!page.template) page.elt.normalize(); // normalize HTML (merge text nodes) - don't touch template pages or it can break Vue
    }

    // Restore selection and remove empty elements
    if (document.body.contains(start_marker)) {
      const range = document.createRange();
      range.setStart(start_marker, 0);
      if (document.body.contains(end_marker)) range.setEnd(end_marker, 0);
      selection.removeAllRanges();
      selection.addRange(range);
    }
    if (start_marker.parentElement) start_marker.parentElement.removeChild(start_marker);
    if (end_marker.parentElement) end_marker.parentElement.removeChild(end_marker);

    // Store pages HTML content
    for (const page of pages) {
      page.prev_innerHTML = page.elt.innerHTML; // store current pages innerHTML for next call
    }

    // Clear "fit in progress" flag
    fit_in_progress = false;
  }

  function inputEventHandler(e) {
    if (!e) {
      return;
    }
    fit_content_over_pages(); // fit content according to modifications
    emit_new_content(); // emit content modification
    if (e.inputType !== 'insertText') {
      // update current style if it has changed
      process_current_text_style();
    }
  }

  function pasteEventHandler(e) {

    const clipboardData = e.clipboardData || window.clipboardData

    let pastedData = clipboardData.getData('Text')
    e.preventDefault()

    pastedData = pastedData.replace(/\n/g, ' ')
      .trim()
      .replace(/\s+/g, ' ')

    document.execCommand('insertHTML', false, pastedData)
  }

  return {
    init(documentEditorElement, documentContentElement, documentContent, documentConfig) {

      editorElement = documentEditorElement
      contentElement = documentContentElement

      content = documentContent || ['']
      Object.keys(documentConfig).forEach(item => config[item] = documentConfig[item])

      contentElement.removeEventListener('input', inputEventHandler)
      contentElement.addEventListener('input', inputEventHandler)

      contentElement.removeEventListener('paste', pasteEventHandler)
      contentElement.addEventListener('paste', pasteEventHandler)

      update_editor_width()
      reset_content()
    }
  }
})();
