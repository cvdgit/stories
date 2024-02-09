import {_extends, shuffle} from "../common";
import Sortable from "sortablejs";

function Grouping(test) {
  this.element = null;
  this.container = test.container;
  this.deck = test.getDeck();
}

Grouping.prototype.createWrapper = function (content) {
  const $wrapper = $('<div class="seq-question image-gaps-question"></div>');
  if (content) {
    $wrapper.append(content);
  }
  return $wrapper;
};

Grouping.prototype.create = function (question) {

  const {payload} = question
  const {groups} = payload

  function getGroupsClassName(groupsLen) {
    switch (groupsLen) {
      case 2: return "two-groups"
      case 3: return "three-groups"
      case 4: return "four-groups"
      case 5: return "five-groups"
      default: return "two-groups"
    }
  }

  const $groups = $('<div/>', {class: "grouping-groups"})
  $groups.addClass(getGroupsClassName(groups.length))

  let items = []
  groups.map(group => {

    items = [...items, ...group.items]
    const $group = $("<div/>", {
      class: "grouping-group",
      "data-group-id": group.id
    })
      .append(
        $("<p/>").text(group.title)
      )
      .append(
        $("<div/>", {class: "group-items-spot"})
      )

    new Sortable($group.find(".group-items-spot")[0], {
      sort: false,
      forceFallback: true,
      group: {
        name: 'shared'
      }
    });

    $group.appendTo($groups)
  })

  const $groupItems = $('<div/>', {class: "grouping-items"})

  items = shuffle(items)
  items.map(item => {
    const $item = $('<button/>', {
      type: 'button',
      class: 'pass-test-btn highlight',
      "data-item-id": item.id,
      text: item.title
    })
    $groupItems.append($item)
  })

  new Sortable($groupItems[0], {
    sort: false,
    forceFallback: true,
    group: {
      name: 'shared',
      put: false
    }
  });

  const $wrap = $('<div/>', {class: "grouping-wrap"})
  this.element = $wrap
    .append($groups)
    .append($groupItems)

  return this.element;
};

Grouping.prototype.getUserAnswers = function() {
  return this.element.find(".grouping-group").map((i, elem) => {
    return {
      groupId: $(elem).attr("data-group-id"),
      items: $(elem).find(".highlight").map((j, item) => {
        return $(item).attr("data-item-id")
      }).get()
    }
  }).get()
}

_extends(Grouping, {
  pluginName: 'groupingQuestion'
});

export default Grouping
