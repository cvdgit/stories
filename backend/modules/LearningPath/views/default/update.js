(function () {

  const learningPath = {}

  async function initTree($el) {
    let CLIPBOARD = null;
    const treeKey = $el.find('.tree').attr('data-tree')

    const response = await fetch(`/admin/index.php?r=learning-path/default/data&id=c1fab246-7bc4-4e8b-b8a7-9abd8d7ba35f&key=${treeKey}`, {
      method: 'get',
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      }
    })

    learningPath[treeKey] = await response.json()

    $el.find('.tree-name').text(learningPath[treeKey].name)

    const $tree = $el.find('.tree')

    $tree
      .fancytree({
        checkbox: false,
        icon: false,
        //selectMode: 3,
        source: learningPath[treeKey].items,
        extensions: ['edit', 'dnd5', 'glyph'],
        glyph: {
          preset: "bootstrap3",
          map: {}
        },
        /*postProcess: function(event, data) {
          learningPath[treeKey] = data.response
        },*/
        modifyChild: function(event, data) {
          data.tree.info(event.type, data)
          learningPath[treeKey].items = data.tree.toDict(false)

          fetch(`/admin/index.php?r=learning-path/default/save&id=c1fab246-7bc4-4e8b-b8a7-9abd8d7ba35f`, {
            method: 'post',
            body: JSON.stringify({payload: learningPath}),
            cache: 'no-cache',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
          })
            .then((response) => {
              if (response.ok) {
                return response.json();
              }
              throw new Error(response.statusText);
            })
            .then((responseJson) => {
              if (responseJson.success) {
                toastr.success(responseJson.message || 'Успешно');
              }
              else {
                toastr.error(responseJson.message || 'Ошибка');
              }
            })
            .catch((error) => {
              toastr.error(error);
            });

        },
        edit: {
          triggerStart: ["f2", "shift+click", "mac+enter"],
          inputCss: { minWidth: "3em" },
          adjustWidthOfs: 4,
          beforeEdit: function(event, data){
            data.node.setActive()
          },
          close: function (event, data) {
            if (data.save && data.isNew) {
              // Quick-enter: add new nodes until we hit [enter] on an empty title
              $tree.trigger("nodeCommand", {
                cmd: "addSibling",
              });
            }
          },
        },
      })
      .on("nodeCommand", function(event, data) {
        // Custom event handler that is triggered by keydown-handler and
        // context menu:
        var refNode,
          moveMode,
          tree = $.ui.fancytree.getTree(this),
          node = tree.getActiveNode();
        switch (data.cmd) {
          case "addChild":
          case "addSibling":
          case "indent":
          case "moveDown":
          case "moveUp":
          case "outdent":
          case "remove":
          case "rename":
            tree.applyCommand(data.cmd, node);
            break;
          case "cut":
            CLIPBOARD = {mode: data.cmd, data: node};
            break;
          case "copy":
            CLIPBOARD = {
              mode: data.cmd,
              data: node.toDict(true, function (dict, node) {
                delete dict.key;
              }),
            };
            break;
          case "clear":
            CLIPBOARD = null;
            break;
          case "paste":
            if (CLIPBOARD.mode === "cut") {
              // refNode = node.getPrevSibling();
              CLIPBOARD.data.moveTo(node, "child");
              CLIPBOARD.data.setActive();
            } else if (CLIPBOARD.mode === "copy") {
              node.addChildren(
                CLIPBOARD.data
              ).setActive();
            }
            break;
          default:
            alert("Unhandled command: " + data.cmd);
            return;
        }
      })

    $tree.contextmenu({
      delegate: "span.fancytree-node",
      menu: [
        {
          title: "Изменить",
          cmd: "rename",
          uiIcon: "ui-icon-pencil",
        },
        {
          title: "Удалить",
          cmd: "remove",
          uiIcon: "ui-icon-trash",
        },
        {title: "----"},
        {
          title: "Добавить соседний элемент",
          cmd: "addSibling",
          uiIcon: "ui-icon-plus",
        },
        {
          title: "Добавить дочерний элемент",
          cmd: "addChild",
          uiIcon: "ui-icon-arrowreturn-1-e",
        },
        {title: "----"},
        {
          title: "Вырезать",
          cmd: "cut",
          uiIcon: "ui-icon-scissors",
        },
        {
          title: "Копировать",
          cmd: "copy",
          uiIcon: "ui-icon-copy",
        },
        {
          title: "Вставить",
          cmd: "paste",
          uiIcon: "ui-icon-clipboard",
          disabled: true,
        },
      ],
      beforeOpen: function (event, ui) {
        const node = $.ui.fancytree.getNode(ui.target);
        $tree.contextmenu(
          "enableEntry",
          "paste",
          !!CLIPBOARD
        );
        node.setActive();
      },
      select: function (event, ui) {
        const that = this;
        // delay the event, so the menu can close and the click event does
        // not interfere with the edit control
        setTimeout(function () {
          $(that).trigger("nodeCommand", {cmd: ui.cmd});
        }, 100);
      },
    })
  }

  $('.tree-wrap').each((i, el) => {
    initTree($(el))
  })

  async function createTree(treeId, name) {
    await fetch(`/admin/index.php?r=learning-path/default/create-tree&id=c1fab246-7bc4-4e8b-b8a7-9abd8d7ba35f`, {
      method: 'post',
      body: JSON.stringify({
        tree: treeId,
        name
      }),
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      }
    })
  }

  $('#add-tree').on('click', async () => {
    const treeLength = $('.tree-container').find('.tree-wrap').length + 1
    const treeId = `tree${treeLength}`

    const $treeWrap = $(`<div class="tree-wrap">
    <div class="tree-actions">
        <div class="tree-name" contenteditable="plaintext-only"></div>
        <button class="tree-delete" type="button">&times;</button>
    </div>
    <div class="tree" data-tree="${treeId}"></div>
</div>
    `)

    if (treeLength === 1) {
      $('.tree-container').prepend($treeWrap)
    } else {
      $('.tree-container').find('.tree-wrap:last').after($treeWrap)
    }

    await createTree(treeId, 'new tree')

    initTree($treeWrap)
  })

  async function saveTreeName(treeId, name) {
    await fetch(`/admin/index.php?r=learning-path/default/save-tree-name&id=c1fab246-7bc4-4e8b-b8a7-9abd8d7ba35f`, {
      method: 'post',
      body: JSON.stringify({
        tree: treeId,
        name
      }),
      cache: 'no-cache',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
      }
    })
  }

  const inputMap = new Map()
  $('.tree-container')
    .on('input', '.tree-name', async (e) => {
    const treeId = $(e.target).parent().parent().find('.tree').attr('data-tree')
    const id = inputMap.get(treeId)
    if (id) {
      clearTimeout(id)
    }
    const timeoutId = setTimeout(() => {
      saveTreeName(treeId, e.target.innerText)
    }, 300)
    inputMap.set(treeId, timeoutId)
  })
    .on('click', '.tree-delete', async (e) => {
      const treeId = $(e.target).parent().parent().find('.tree').attr('data-tree')
      const response = await fetch(`/admin/index.php?r=learning-path/default/delete-tree&id=c1fab246-7bc4-4e8b-b8a7-9abd8d7ba35f`, {
        method: 'post',
        body: JSON.stringify({
          tree: treeId,
        }),
        cache: 'no-cache',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
        }
      })
      if (!response.ok) {
        alert('error')
      }
      const json = await response.json()
      if (json.success) {
        const tree = $.ui.fancytree.getTree(`.tree[data-tree=${treeId}]`)
        tree.destroy()
        $(e.target).parent().parent().remove()
      }
    })
})()
