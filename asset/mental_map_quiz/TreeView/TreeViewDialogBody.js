import "./TreeViewBody.css";
import "./TreeViewDialogBody.css";
import MapImageStatus from "../components/MapImageStatus";

function createRow(node, status, done) {

  const row = document.createElement('div')
  row.dataset.nodeId = node.id
  row.dataset.level = node.level
  row.classList.add('node-row')

  if (done) {
    row.classList.add('node-row-done');
  }

  row.innerHTML = `
<div class="node-status"></div>
<div class="node-body">
<div class="node-title" style="cursor: pointer">${node.title.replace(/\r?\n/g, "\r\n")}</div>
</div>
  `;

  row.querySelector('.node-status').appendChild(status);

  return row;
}

function flatten(nodes, level = 0) {
  return nodes.flatMap((node, index) => [
    {...node, level, index, hasChildren: (node.children || []).length > 0},
    ...flatten(node.children || [], level + 1)
  ])
}

function flattenTree(nodes, level = 0, parentExpanded = true, parentLastChilds = []) {
  let result = [];

  nodes.forEach((node, index) => {
    const isLastChild = index === nodes.length - 1;
    const isFirstChild = level === 0 && index === 0;

    const currentParentLastChilds = [...parentLastChilds, isLastChild];

    result.push({
      ...node,
      level,
      isFirstChild,
      isLastChild,
      parentLastChilds,
      isVisible: parentExpanded
    });

    if (node.children?.length) {
      result = result.concat(
        flattenTree(
          node.children,
          level + 1,
          parentExpanded && node.expanded,
          currentParentLastChilds
        )
      );
    }
  });

  return result;
}

function renderStructure(node) {
  const container = document.createElement('div');
  container.className = 'tree-structure';

  node.parentLastChilds.forEach(isLast => {
    const line = document.createElement('div');
    line.className = 'tree-line';
    if (!isLast) {
      line.classList.add('line-vertical');
    }
    container.appendChild(line);
  });

  const join = document.createElement('div');
  join.className = 'tree-join';

  join.classList.add(
    node.isLastChild ? 'join-last' : 'join-middle'
  );

  if (node.isFirstChild) {
    join.classList.add('join-first');
  }

  container.appendChild(join);

  return container;
}

function renderRow(node, renderCustomNode, toggleHandler) {
  const row = document.createElement('div');
  row.className = 'tree-row';
  row.dataset.imgId = node.id;

  if (!node.isVisible) {
    row.style.display = 'none';
  }

  const structure = renderStructure(node);

  const content = document.createElement('div');
  content.className = 'tree-content';

  if (node.children?.length) {
    const toggle = document.createElement('div');
    toggle.classList.add('toggle', node.expanded ? 'toggle__collapseButton' : 'toggle__expandButton');
    toggle.dataset.toggleId = node.id;
    toggle.onclick = () => {
      toggleHandler(node.id);
    }
    content.appendChild(toggle);
  }

  content.appendChild(renderCustomNode(node));

  row.appendChild(structure);
  row.appendChild(content);

  return row;
}

function findNode(nodes, id) {
  for (const node of nodes) {
    if (node.id === id) return node;
    if (node.children?.length) {
      const found = findNode(node.children, id);
      if (found) return found;
    }
  }
  return null;
}

function updateVisibility(tree) {
  const list = flattenTree(tree);
  list.forEach(node => {
    const row = document.querySelector(`[data-img-id="${node.id}"]`);
    if (!row) return;
    row.style.display = node.isVisible ? '' : 'none';
    const toggle = row.querySelector('.toggle');
    if (toggle && node.children?.length) {
      toggle.classList.remove('toggle__collapseButton', 'toggle__expandButton');
      toggle.classList.add(
        node.expanded ? 'toggle__collapseButton' : 'toggle__expandButton'
      );
    }
  });
}

function toggleNode(tree, nodeId) {
  const node = findNode(tree, nodeId);
  node.expanded = !node.expanded;
  updateVisibility(
    tree
  );
}

async function getHistoryLog(id) {
  return await window.Api.get(`/mental-map/item-log?id=${id}`);
}

export default function TreeViewDialogBody({tree, voiceResponse, history, itemClickHandler, params, onEndHandler, isPlanTreeView, settingsPromptId}) {

  function init() {
    const body = document.createElement('div');
    body.classList.add('tree-body', 'tree-dialog-body');
    return body;
  }

  let body = init();

  voiceResponse.onError(({args}) => {
    const row = body.querySelector('.node-row.current-row.pending')
    if (row) {
      body.classList.remove('do-recording')
      const id = row.getAttribute('data-node-id')
      const historyItem = history.find(h => h.id === id)
      historyItem.pending = false
      body.innerHTML = ''
      API.init()
    }
  })

  const API = {
    getElement() {
      return body
    },
    init() {
      const list = flattenTree(tree);
      list.map(node => {

        const historyItem = history.find(({id}) => id === node.id);
        const row = createRow(node, MapImageStatus.render({
          hiding: historyItem.hiding || 0,
          seconds: historyItem.seconds || 0,
          hidingPrev: historyItem.hidingPrev || 0,
        }), historyItem.done);

        row.querySelector('.node-title').addEventListener('click', e => {

          body.querySelectorAll('.node-row')
            .forEach(elem => elem.classList.remove('node-active'));
          row.classList.add('node-active');

          itemClickHandler({
            id: node.id,
            text: node.description,
            description: node.description
          });
        });

        /*const stat = row.querySelector('.map-user-status-hiding');
        if (stat) {
          stat.addEventListener('click', async () => {
            console.log(await getHistoryLog(node.id));
          });
        }*/

        body.appendChild(
          renderRow(
            node,
            () => row,
            nodeId => {
              toggleNode(tree, nodeId);
            }
          )
        );
      });
    },
    restart() {
      body.remove()
      history = history.map(h => ({...h, done: false, all: 0, pending: false, repeat: false}))
      body = init()
      this.init()
      return body
    },
    on(type, listener, useCapture) {
      body.addEventListener(type, listener, useCapture)
    }
  }

  return API;
}
