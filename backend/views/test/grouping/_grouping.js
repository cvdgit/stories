(function () {

  function generateUUID() {
    var d = new Date().getTime();
    var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
      var r = Math.random() * 16;
      if (d > 0) {
        r = (d + r) % 16 | 0;
        d = Math.floor(d / 16);
      } else {
        r = (d2 + r) % 16 | 0;
        d2 = Math.floor(d2 / 16);
      }
      return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
  }

  function GroupingManager(groups) {

    const state = {...groups || {groups: []}}

    this.getGroups = () => {
      return [...state.groups]
    }

    this.getState = () => {
      return {...state}
    }

    this.addGroup = group => state.groups.push(group)

    this.createEmptyGroup = (id, title) => {
      return {id, title, items: [], index: state.groups.length + 1}
    }

    this.addGroupItem = (groupId, item) => state.groups.map(g => {
      if (g.id === groupId) {
        return {...g, items: g.items.push(item)}
      }
      return g
    })

    this.updateGroup = (id, group) => {
      state.groups = state.groups.map(g => {
        if (g.id === id) {
          return {...g, ...group}
        }
        return g
      })
    }

    this.updateGroupItem = (groupId, itemId, item) => {
      state.groups = state.groups.map(g => {
        if (g.id === groupId) {
          return {
            ...g, items: g.items.map(i => {
              if (i.id === itemId) {
                return {...i, ...item}
              }
              return i
            })
          }
        }
        return g
      })
    }

    this.moveGroup = (newIndex, oldIndex) => {
      state.groups.splice(oldIndex, 0, state.groups.splice(newIndex, 1)[0])
    }

    this.deleteGroup = (id) => {
      state.groups = state.groups.filter(g => g.id !== id)
    }

    this.deleteGroupItem = (groupId, itemId) => {
      state.groups = state.groups.map(g => {
        if (g.id === groupId) {
          return {...g, items: g.items.filter(i => i.id !== itemId)}
        }
        return g
      })
    }
  }

  const groupingManager = new GroupingManager(window["groupingData"])

  function GroupRenderer(container) {
    this.container = container

    this.appendGroup = ({id, title, index, items, addItemHandler, changeTitleHandler, changeItemTitleHandler, deleteHandler, deleteItemHandler}) => {

      const html = `<div class="grouping-item" data-id="${id}">
    <div class="grouping-item-inner">
        <div style="width: 20px">
        <div class="handle">
        <svg viewBox="0 0 4 24" width="4" height="24" focusable="false">
                <title>Vertical Dots</title>
                <desc>Vertical Dots</desc>
                <path fill-rule="evenodd"
                      d="M2 24a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0-10a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM2 4a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path>
            </svg>
</div>

        </div>
        <div style="min-width: 30px">
            <span class="group-number">${index}</span>
        </div>
        <div style="width: 100%">
            <div style="display: flex; flex-direction: row; justify-content: space-between">
                <div style="width: 100%">
                    <label class="control-label">Группа: </label>
                    <input class="form-control group-title" type="text" value="${title}"/>
                </div>
                <div style="display: flex; align-items: center; justify-content: center; width: 50px">
                    <a style="width: 20px" href="#" class="delete-group text-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div style="padding: 20px 0">
                <div>
                    <label for="">Элементы:</label>
                </div>
                <div class="grouping-item-list">
                    <p class="no-group-items">Пусто</p>
                </div>
            </div>
            <div>
                <button class="btn btn-success btn-sm add-group-item" type="button">Добавить элемент</button>
            </div>
        </div>
    </div>
</div>
`

      if (this.container.find(".no-groups").length) {
        this.container.empty()
      }

      this.container.append(html)
      const $element = this.container.find(`[data-id="${id}"]`)

      const $itemList = $element.find(".grouping-item-list")

      $element.find(".group-title").on("input", (e) => {
        changeTitleHandler(e.target.value)
      })

      $element.find(".add-group-item").on("click", () => {
        if ($itemList.find(".no-group-items").length) {
          $itemList.empty()
        }

        const item = {id: generateUUID(), title: "Элемент"}
        this.appendGroupItem(id, item, changeItemTitleHandler, deleteItemHandler)
        addItemHandler(item)
      })

      $element.find(".delete-group").on("click", (e) => {
        e.preventDefault()
        try {
          deleteHandler(id)
          console.log("qwe")
          $element.remove()
        } catch (ex) {}
      })

      $itemList.empty()
      items = items || []
      items.map(item => this.appendGroupItem(id, item, changeItemTitleHandler, deleteItemHandler))

      return $element
    }

    this.appendGroupItem = (groupId, item, changeItemTitleHandler, deleteItemHandler) => {
      const $item = $(`<div class="grouping-item-item">
    <div style="width: 100%">
        <input class="form-control group-item-title" type="text" value="${item.title}"/>
    </div>
    <div style="width: 50px; display: flex; align-items: center; justify-content: center">
        <a style="width: 20px" href="#" class="group-item-delete text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </a>
    </div>
</div>`)
      $item.find(".group-item-title").on("input", (e) => {
        changeItemTitleHandler(item.id, e.target.value)
      })
      $item.find(".group-item-delete").on("click", (e) => {
        e.preventDefault()
        $item.remove()
        deleteItemHandler(groupId, item.id)
      })
      this.container
        .find(`[data-id="${groupId}"] .grouping-item-list`)
        .append($item)
    }

    this.resetGroupIndex = () => {
      this.container.find(".grouping-item").map((i, element) => {
        $(element).find(".group-number").text(i + 1)
      })
    }
  }

  const renderer = new GroupRenderer($(".grouping-wrap"))

  $("#add-group").on("click", function () {

    if (groupingManager.getGroups().length >= 5) {
      toastr.warning("Можно создать только 5 групп")
      return
    }

    const group = groupingManager.createEmptyGroup(generateUUID(), "")
    groupingManager.addGroup(group)
    renderer.appendGroup({
      ...group,
      addItemHandler: (item) => addGroupItemHandler(group.id, item),
      changeTitleHandler: (value) => changeGroupTitleHandler(group.id, value),
      changeItemTitleHandler: (id, value) => changeGroupItemTitleHandler(group.id, id, value),
      deleteHandler: deleteGroupHandler,
      deleteItemHandler: deleteGroupItemHandler,
    })
  })

  const addGroupItemHandler = (groupId, item) => {
    groupingManager.addGroupItem(groupId, item)
  }

  const changeGroupTitleHandler = (id, title) => groupingManager.updateGroup(id, {title})

  const changeGroupItemTitleHandler = (groupId, itemId, title) => groupingManager.updateGroupItem(groupId, itemId, {title})

  const deleteGroupHandler = (id) => {
    if (groupingManager.getGroups().length <= 2) {
      toastr.warning("Должно быть минимум две группы")
      throw new Error("Должно быть минимум две группы")
    }
    groupingManager.deleteGroup(id)
    renderer.resetGroupIndex()
  }

  const deleteGroupItemHandler = (groupId, itemId) => groupingManager.deleteGroupItem(groupId, itemId)

  groupingManager.getGroups().map((group, index) => {
    renderer.appendGroup({
      index: index + 1,
      ...group,
      addItemHandler: (item) => addGroupItemHandler(group.id, item),
      changeTitleHandler: (value) => changeGroupTitleHandler(group.id, value),
      changeItemTitleHandler: (id, value) => changeGroupItemTitleHandler(group.id, id, value),
      deleteHandler: deleteGroupHandler,
      deleteItemHandler: deleteGroupItemHandler,
    })
  })

  Sortable.create($(".grouping-wrap")[0], {
    handle: '.handle',
    animation: 300,
    swapThreshold: 0.75,
    onUpdate: function (e) {
      groupingManager.moveGroup(e.oldIndex, e.newIndex)
      renderer.resetGroupIndex()
    },
    onStart: function () {
      $('nav#w0').hide();
    },
    onEnd: function () {
      $('nav#w0').show();
    }
  });

  attachBeforeSubmit(document.getElementById("grouping-form"), (form) => {

    if (groupingManager.getGroups().length === 0) {
      toastr.warning("Необходимо создать группы")
      return
    }

    if (groupingManager.getGroups().length < 2) {
      toastr.warning("Должно быть минимум две группы")
      return
    }

    if (groupingManager.getGroups().length > 5) {
      toastr.warning("Можно создать только 5 групп")
      return
    }

    const groupWithEmptyItems = groupingManager.getGroups()
      .filter(g => g.items.filter(i => i.title.trim() === "").length > 0)
    if (groupWithEmptyItems.length > 0) {
      toastr.warning("Не должно быть элементов с пустыми значениями")
      return
    }

    const emptyGroups = groupingManager.getGroups().filter(g => g.items.length === 0)
    if (emptyGroups.length > 0) {
      toastr.warning("Не должно быть пустых групп")
      return
    }

    let allItems = []
    groupingManager.getGroups().map(g => allItems = [...allItems, ...(g.items.map(i => i.title.trim()))])
    const haveDuplicates = allItems.some((element, index) => allItems.indexOf(element) !== index)
    if (haveDuplicates) {
      toastr.warning("Не должно быть повторяющихся элементов")
      return
    }

    const formData = new FormData(form)
    formData.set($(form).find("#grouping_payload").attr("name"), JSON.stringify(groupingManager.getState()))
    sendForm($(form).attr("action"), $(form).attr("method"), formData)
      .then(response => {
        if (response && response.success) {
          if (response.url) {
            location.replace(response.url);
          } else {
            toastr.success('Успешно');
          }
        } else {
          toastr.error(response['message'] || 'Неизвестная ошибка');
        }
      })
  })
})()
